# HealthTrack setup logic.
#
# Deliberately contains no user interface. gui.ps1 is a thin front-end over
# these functions, so anything the window can do can also be done from a
# terminal -- useful when the GUI itself is the thing that is broken.
#
# Every long-running function takes a -Log scriptblock. The GUI passes one that
# appends to its output pane; from a terminal, pass { param($m) Write-Host $m }.
#
# Windows PowerShell 5.1: no ternary, no ?? operator, no && chaining.

# No Set-StrictMode here on purpose: this file is meant to be dot-sourced into
# whatever shell you happen to have open, and changing that shell's strictness
# as a side effect is rude (it breaks unrelated scripts). gui.ps1 sets it for
# its own process, which covers these functions when the window runs them.

# Where we put things we install ourselves. Under the user profile so none of
# this needs administrator rights.
$script:ToolsDir = Join-Path $env:USERPROFILE 'tools'
$script:PhpDir   = Join-Path $script:ToolsDir 'php84'

# Pinned so a stale winget manifest cannot break the install. Bump by hand.
$script:PhpVersion = '8.4.23'
$script:PhpUrl     = "https://windows.php.net/downloads/releases/php-$($script:PhpVersion)-Win32-vs17-x64.zip"

function Get-ProjectRoot {
    # tools/setup/lib.ps1 -> tools/setup -> tools -> project root
    return (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
}

function Write-Log {
    param([scriptblock]$Log, [string]$Message)
    if ($Log) { & $Log $Message } else { Write-Host $Message }
}

# --- Locating tools -------------------------------------------------------

function Find-Tool {
    <#
        Look for an executable on PATH first, then in the places we install to.
        Returns the full path, or $null.
    #>
    param([string]$Name, [string[]]$Candidates = @())

    $onPath = Get-Command $Name -ErrorAction SilentlyContinue
    if ($onPath) { return $onPath.Source }

    foreach ($c in $Candidates) {
        if ($c -and (Test-Path $c)) { return (Resolve-Path $c).Path }
    }
    return $null
}

function Find-Php {
    return Find-Tool -Name 'php' -Candidates @((Join-Path $script:PhpDir 'php.exe'))
}

function Find-Composer {
    # composer.phar is not executable on its own; callers run "php composer.phar".
    $phar = Join-Path $script:ToolsDir 'composer.phar'
    if (Test-Path $phar) { return $phar }
    return Find-Tool -Name 'composer'
}

function Find-Npm {
    return Find-Tool -Name 'npm' -Candidates @('C:\Program Files\nodejs\npm.cmd')
}

function Find-Psql {
    $installed = @()
    if (Test-Path 'C:\Program Files\PostgreSQL') {
        # Newest major version first.
        $installed = Get-ChildItem 'C:\Program Files\PostgreSQL' -Directory |
            Sort-Object Name -Descending |
            ForEach-Object { Join-Path $_.FullName 'bin\psql.exe' }
    }
    return Find-Tool -Name 'psql' -Candidates $installed
}

function Get-Prerequisites {
    <#
        One row per required tool, for the status table in the GUI.
    #>
    $php      = Find-Php
    $composer = Find-Composer
    $npm      = Find-Npm
    $psql     = Find-Psql

    $rows = @()
    $rows += [pscustomobject]@{ Name = 'PHP 8.3+';   Path = $php;      Required = $true  }
    $rows += [pscustomobject]@{ Name = 'Composer';   Path = $composer; Required = $true  }
    $rows += [pscustomobject]@{ Name = 'Node.js';    Path = $npm;      Required = $true  }
    $rows += [pscustomobject]@{ Name = 'PostgreSQL'; Path = $psql;     Required = $false }

    foreach ($r in $rows) {
        Add-Member -InputObject $r -NotePropertyName 'Found' -NotePropertyValue ([bool]$r.Path)
    }
    return $rows
}

function Get-ProjectStatus {
    <#
        What state the checkout itself is in, independent of the tools.
    #>
    $root = Get-ProjectRoot
    $envPath = Join-Path $root '.env'

    $hasKey = $false
    if (Test-Path $envPath) {
        $line = Select-String -Path $envPath -Pattern '^APP_KEY=base64:' -Quiet
        if ($line) { $hasKey = $true }
    }

    $rows = @()
    $rows += [pscustomobject]@{ Name = 'Dependencies (vendor/)'; Found = (Test-Path (Join-Path $root 'vendor')) }
    $rows += [pscustomobject]@{ Name = 'Front-end (node_modules/)'; Found = (Test-Path (Join-Path $root 'node_modules')) }
    $rows += [pscustomobject]@{ Name = 'Environment file (.env)'; Found = (Test-Path $envPath) }
    $rows += [pscustomobject]@{ Name = 'Application key'; Found = $hasKey }
    $rows += [pscustomobject]@{ Name = 'Compiled assets'; Found = (Test-Path (Join-Path $root 'public\build\manifest.json')) }
    return $rows
}

# --- Running external commands -------------------------------------------

function ConvertTo-ProcessArgument {
    <#
        Quote a single argument for Start-Process.

        -ArgumentList joins an array with plain spaces and quotes nothing, so
        any argument containing a space arrives at the child as several
        separate arguments. That silently broke every psql -c "SELECT ..."
        call, and would break any path under "C:\Users\First Last\".

        Follows the Windows convention: wrap in double quotes, double any
        backslashes that immediately precede a quote, and escape the quote.
    #>
    param([string]$Value)

    if ($null -eq $Value -or $Value -eq '') { return '""' }
    if ($Value -notmatch '[\s"]') { return $Value }

    $escaped = [regex]::Replace($Value, '(\\*)"', '$1$1\"')
    $escaped = [regex]::Replace($escaped, '(\\+)$', '$1$1')
    return '"' + $escaped + '"'
}

function Invoke-Step {
    <#
        Run a process, streaming its output through -Log as it arrives.

        Output is tailed from a temp file rather than read from the pipe
        directly: reading stdout and stderr synchronously can deadlock when one
        buffer fills, and tailing sidesteps that entirely.

        Returns the exit code.
    #>
    param(
        [Parameter(Mandatory = $true)][string]$FilePath,
        [string[]]$Arguments = @(),
        [string]$WorkingDirectory,
        [hashtable]$Environment,
        [scriptblock]$Log
    )

    if (-not $WorkingDirectory) { $WorkingDirectory = Get-ProjectRoot }

    Write-Log $Log "> $(Split-Path $FilePath -Leaf) $($Arguments -join ' ')"

    # Environment overrides (e.g. PGPASSWORD) apply to the child process, which
    # inherits from us, so set and restore around the call.
    $saved = @{}
    if ($Environment) {
        foreach ($k in $Environment.Keys) {
            $saved[$k] = [Environment]::GetEnvironmentVariable($k)
            [Environment]::SetEnvironmentVariable($k, $Environment[$k])
        }
    }

    $outFile = [IO.Path]::GetTempFileName()
    $errFile = [IO.Path]::GetTempFileName()
    $exit = -1

    try {
        $startArgs = @{
            FilePath               = $FilePath
            WorkingDirectory       = $WorkingDirectory
            NoNewWindow            = $true
            PassThru               = $true
            RedirectStandardOutput = $outFile
            RedirectStandardError  = $errFile
        }
        if ($Arguments.Count -gt 0) {
            $startArgs['ArgumentList'] = @($Arguments | ForEach-Object { ConvertTo-ProcessArgument $_ })
        }

        $proc = Start-Process @startArgs

        # Reading .Handle forces the Process object to hold the OS handle open.
        # Without it, Start-Process -PassThru returns an object whose ExitCode
        # is empty once the process ends -- and since ($null -ne 0) is true,
        # every caller would read a clean run as a failure.
        $null = $proc.Handle

        $pos = 0
        while (-not $proc.HasExited) {
            $pos = Send-NewOutput -Path $outFile -Position $pos -Log $Log
            Start-Sleep -Milliseconds 120
        }
        $proc.WaitForExit()
        Send-NewOutput -Path $outFile -Position $pos -Log $Log | Out-Null

        $errText = Get-Content $errFile -Raw -ErrorAction SilentlyContinue
        if ($errText -and $errText.Trim()) {
            foreach ($l in ($errText -split "`r?`n")) {
                if ($l.Trim()) { Write-Log $Log "  $l" }
            }
        }

        $exit = $proc.ExitCode
    }
    finally {
        if ($Environment) {
            foreach ($k in $saved.Keys) { [Environment]::SetEnvironmentVariable($k, $saved[$k]) }
        }
        Remove-Item $outFile, $errFile -ErrorAction SilentlyContinue
    }

    if ($exit -ne 0) { Write-Log $Log "  (exit code $exit)" }
    return $exit
}

function Send-NewOutput {
    <# Emit whatever has been appended to $Path since $Position. Returns the new position. #>
    param([string]$Path, [int]$Position, [scriptblock]$Log)

    if (-not (Test-Path $Path)) { return $Position }
    $text = Get-Content $Path -Raw -ErrorAction SilentlyContinue
    if (-not $text) { return $Position }
    if ($text.Length -le $Position) { return $Position }

    $chunk = $text.Substring($Position)
    foreach ($line in ($chunk -split "`r?`n")) {
        if ($line.Trim()) { Write-Log $Log "  $line" }
    }
    return $text.Length
}

# --- Installing prerequisites --------------------------------------------

function Install-Php {
    param([scriptblock]$Log)

    if (Find-Php) { Write-Log $Log 'PHP already present, skipping.'; return $true }

    Write-Log $Log "Downloading PHP $($script:PhpVersion)..."
    New-Item -ItemType Directory -Force -Path $script:ToolsDir | Out-Null
    $zip = Join-Path $env:TEMP "php-$($script:PhpVersion).zip"

    try {
        $ProgressPreference = 'SilentlyContinue'
        Invoke-WebRequest -Uri $script:PhpUrl -OutFile $zip -UseBasicParsing -TimeoutSec 600
    } catch {
        Write-Log $Log "Download failed: $($_.Exception.Message)"
        Write-Log $Log "PHP may have archived $($script:PhpVersion). Check windows.php.net/downloads/releases and update `$script:PhpVersion in tools/setup/lib.ps1."
        return $false
    }

    Expand-Archive -Path $zip -DestinationPath $script:PhpDir -Force
    Remove-Item $zip -ErrorAction SilentlyContinue

    Set-PhpIni -Log $Log
    Add-ToUserPath -Directory $script:PhpDir -Log $Log

    Write-Log $Log "PHP installed to $($script:PhpDir)."
    return $true
}

function Set-PhpIni {
    <# Enable the extensions Laravel and this project need. #>
    param([scriptblock]$Log)

    $ini = Join-Path $script:PhpDir 'php.ini'
    Copy-Item (Join-Path $script:PhpDir 'php.ini-development') $ini -Force

    $text = Get-Content $ini -Raw
    $text = $text -replace '(?m)^;\s*extension_dir\s*=\s*"ext"', 'extension_dir = "ext"'

    # pdo_pgsql and pgsql are enabled even on a SQLite setup so that switching
    # to PostgreSQL later needs no PHP changes.
    $extensions = @(
        'openssl', 'mbstring', 'fileinfo', 'curl', 'zip',
        'pdo_sqlite', 'sqlite3', 'pdo_pgsql', 'pgsql',
        'intl', 'gd', 'sodium'
    )
    foreach ($e in $extensions) {
        $text = $text -replace "(?m)^;extension=$([regex]::Escape($e))\s*$", "extension=$e"
    }

    Set-Content $ini -Value $text -Encoding utf8
    Write-Log $Log 'php.ini configured.'
}

function Install-Composer {
    param([scriptblock]$Log)

    if (Find-Composer) { Write-Log $Log 'Composer already present, skipping.'; return $true }

    $php = Find-Php
    if (-not $php) { Write-Log $Log 'Composer needs PHP. Install PHP first.'; return $false }

    Write-Log $Log 'Downloading Composer installer...'
    New-Item -ItemType Directory -Force -Path $script:ToolsDir | Out-Null
    $setup = Join-Path $env:TEMP 'composer-setup.php'

    try {
        [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
        $ProgressPreference = 'SilentlyContinue'
        Invoke-WebRequest -Uri 'https://getcomposer.org/installer' -OutFile $setup -UseBasicParsing -TimeoutSec 300
        $expected = (New-Object Net.WebClient).DownloadString('https://composer.github.io/installer.sig').Trim()
    } catch {
        Write-Log $Log "Download failed: $($_.Exception.Message)"
        return $false
    }

    # Verify before executing. Composer publishes this hash precisely so the
    # installer cannot be swapped in transit.
    $escaped = $setup -replace '\\', '\\'
    $actual = (& $php -r "echo hash_file('sha384', '$escaped');").Trim()

    if ($expected -ne $actual) {
        Write-Log $Log 'SIGNATURE MISMATCH -- refusing to run the installer.'
        Write-Log $Log "  expected: $expected"
        Write-Log $Log "  actual:   $actual"
        Remove-Item $setup -ErrorAction SilentlyContinue
        return $false
    }
    Write-Log $Log 'Installer signature verified.'

    & $php $setup "--install-dir=$($script:ToolsDir)" '--filename=composer.phar' '--quiet'
    Remove-Item $setup -ErrorAction SilentlyContinue

    Add-ToUserPath -Directory $script:ToolsDir -Log $Log
    Write-Log $Log 'Composer installed.'
    return $true
}

function Install-Node {
    param([scriptblock]$Log)

    if (Find-Npm) { Write-Log $Log 'Node.js already present, skipping.'; return $true }

    Write-Log $Log 'Installing Node.js via winget (this may show an administrator prompt)...'
    $code = Invoke-Step -FilePath 'winget' -Log $Log -Arguments @(
        'install', '--id', 'OpenJS.NodeJS.LTS', '--exact', '--silent',
        '--accept-package-agreements', '--accept-source-agreements', '--disable-interactivity'
    )
    if ($code -ne 0) { Write-Log $Log 'Node.js install failed.'; return $false }
    return $true
}

function Install-Postgres {
    <#
        Installs the PostgreSQL binaries only. The superuser password is set
        during the installer's own GUI -- this script never chooses or stores
        it. The user types it into the setup window afterwards, and it goes
        straight into .env.
    #>
    param([scriptblock]$Log)

    if (Find-Psql) { Write-Log $Log 'PostgreSQL already present, skipping.'; return $true }

    Write-Log $Log 'Launching the PostgreSQL installer.'
    Write-Log $Log 'Its setup wizard will open and ask you to choose a superuser'
    Write-Log $Log 'password. Remember it, then type the same password into the'
    Write-Log $Log 'Database section here.'

    # --interactive is essential. Without it winget honours the manifest's
    # unattended switches, the wizard never appears, and the superuser password
    # is set to a value nobody ever sees -- which then fails to authenticate.
    $code = Invoke-Step -FilePath 'winget' -Log $Log -Arguments @(
        'install', '--id', 'PostgreSQL.PostgreSQL.18', '--exact', '--interactive',
        '--accept-package-agreements', '--accept-source-agreements'
    )
    if ($code -ne 0) { Write-Log $Log 'PostgreSQL install did not complete.'; return $false }
    return $true
}

function Add-ToUserPath {
    <# Persist a directory on the user's PATH. No administrator rights needed. #>
    param([string]$Directory, [scriptblock]$Log)

    $current = [Environment]::GetEnvironmentVariable('Path', 'User')
    if ($current -and $current.Split(';') -contains $Directory) { return }

    $updated = $Directory
    if ($current) { $updated = "$current;$Directory" }
    [Environment]::SetEnvironmentVariable('Path', $updated, 'User')

    # Also apply to this process so later steps in the same run can find it.
    $env:Path = "$env:Path;$Directory"
    Write-Log $Log "Added to PATH: $Directory"
}

# --- Environment file -----------------------------------------------------

function Set-EnvValue {
    <#
        Set KEY=VALUE in .env, replacing an existing line (even a commented
        one) or appending if absent.
    #>
    param([string]$Path, [string]$Key, [string]$Value)

    # Quote anything with a space, as Laravel's parser expects.
    $written = $Value
    if ($Value -match '\s') { $written = '"' + $Value + '"' }
    $line = "$Key=$written"

    if (-not (Test-Path $Path)) {
        Set-Content $Path -Value $line -Encoding utf8
        return
    }

    $text = Get-Content $Path -Raw
    $pattern = "(?m)^#?\s*$([regex]::Escape($Key))=.*$"

    if ($text -match $pattern) {
        $text = [regex]::Replace($text, $pattern, [System.Text.RegularExpressions.MatchEvaluator]{ param($m) $line })
    } else {
        if (-not $text.EndsWith("`n")) { $text += "`r`n" }
        $text += "$line`r`n"
    }
    Set-Content $Path -Value $text -Encoding utf8 -NoNewline
}

function Initialize-EnvFile {
    param([scriptblock]$Log)

    $root = Get-ProjectRoot
    $envPath = Join-Path $root '.env'
    if (Test-Path $envPath) { return $true }

    $example = Join-Path $root '.env.example'
    if (-not (Test-Path $example)) {
        Write-Log $Log '.env.example is missing -- cannot create .env.'
        return $false
    }
    Copy-Item $example $envPath
    Write-Log $Log 'Created .env from .env.example.'
    return $true
}

function Write-DatabaseConfig {
    <#
        Write the chosen database settings into .env.

        $Password arrives from the GUI's masked field and is written straight
        to .env. It is never logged, and nothing else in this script reads it.
    #>
    param(
        [ValidateSet('sqlite', 'pgsql')][string]$Connection,
        [string]$DbHost = '127.0.0.1',
        [string]$Port = '5432',
        [string]$Database = 'healthtrack',
        [string]$Username = 'postgres',
        [string]$Password = '',
        [scriptblock]$Log
    )

    $root = Get-ProjectRoot
    $envPath = Join-Path $root '.env'
    if (-not (Initialize-EnvFile -Log $Log)) { return $false }

    if ($Connection -eq 'sqlite') {
        $file = Join-Path $root 'database\database.sqlite'
        if (-not (Test-Path $file)) { New-Item -ItemType File $file | Out-Null }

        Set-EnvValue -Path $envPath -Key 'DB_CONNECTION' -Value 'sqlite'
        Set-EnvValue -Path $envPath -Key 'DB_DATABASE'   -Value 'database/database.sqlite'
        Write-Log $Log 'Configured for SQLite.'
    }
    else {
        Set-EnvValue -Path $envPath -Key 'DB_CONNECTION' -Value 'pgsql'
        Set-EnvValue -Path $envPath -Key 'DB_HOST'       -Value $DbHost
        Set-EnvValue -Path $envPath -Key 'DB_PORT'       -Value $Port
        Set-EnvValue -Path $envPath -Key 'DB_DATABASE'   -Value $Database
        Set-EnvValue -Path $envPath -Key 'DB_USERNAME'   -Value $Username
        Set-EnvValue -Path $envPath -Key 'DB_PASSWORD'   -Value $Password
        Write-Log $Log "Configured for PostgreSQL ($DbHost`:$Port/$Database)."
    }
    return $true
}

function New-PostgresDatabase {
    <#
        CREATE DATABASE, if it does not already exist. Uses PGPASSWORD so the
        password never appears in a command line (where it would be visible to
        anything reading the process list).
    #>
    param(
        [string]$DbHost, [string]$Port, [string]$Database,
        [string]$Username, [string]$Password, [scriptblock]$Log
    )

    $psql = Find-Psql
    if (-not $psql) { Write-Log $Log 'psql not found -- install PostgreSQL first.'; return $false }

    $exists = Invoke-Step -FilePath $psql -Log $Log -Environment @{ PGPASSWORD = $Password } -Arguments @(
        '-h', $DbHost, '-p', $Port, '-U', $Username, '-d', 'postgres', '-tAc',
        "SELECT 1 FROM pg_database WHERE datname='$Database'"
    )
    if ($exists -ne 0) {
        Write-Log $Log 'Could not connect to PostgreSQL. Check the host, port, username and password.'
        return $false
    }

    $code = Invoke-Step -FilePath $psql -Log $Log -Environment @{ PGPASSWORD = $Password } -Arguments @(
        '-h', $DbHost, '-p', $Port, '-U', $Username, '-d', 'postgres', '-c',
        "CREATE DATABASE `"$Database`""
    )
    # A duplicate-database error is success as far as we are concerned.
    if ($code -ne 0) { Write-Log $Log "Database '$Database' already exists (or could not be created)." }
    return $true
}

# --- Project setup --------------------------------------------------------

function Install-Dependencies {
    param([scriptblock]$Log)

    $php = Find-Php; $composer = Find-Composer; $npm = Find-Npm
    if (-not $php)      { Write-Log $Log 'PHP is missing.';      return $false }
    if (-not $composer) { Write-Log $Log 'Composer is missing.'; return $false }
    if (-not $npm)      { Write-Log $Log 'Node.js is missing.';  return $false }

    Write-Log $Log 'Installing PHP dependencies...'
    if ((Invoke-Step -FilePath $php -Arguments @($composer, 'install', '--no-interaction') -Log $Log) -ne 0) { return $false }

    Write-Log $Log 'Installing front-end dependencies...'
    if ((Invoke-Step -FilePath $npm -Arguments @('install', '--no-audit', '--no-fund') -Log $Log) -ne 0) { return $false }

    return $true
}

function Initialize-Application {
    <# Key, migrations, demo data, compiled assets. #>
    param([switch]$Fresh, [scriptblock]$Log)

    $php = Find-Php
    $npm = Find-Npm
    $root = Get-ProjectRoot
    if (-not $php) { Write-Log $Log 'PHP is missing.'; return $false }

    if (-not (Initialize-EnvFile -Log $Log)) { return $false }

    $envPath = Join-Path $root '.env'
    if (-not (Select-String -Path $envPath -Pattern '^APP_KEY=base64:' -Quiet)) {
        Write-Log $Log 'Generating application key...'
        Invoke-Step -FilePath $php -Arguments @('artisan', 'key:generate', '--no-interaction') -Log $Log | Out-Null
    }

    # Config is cached like any other; a stale cache silently ignores the .env
    # we just wrote.
    Invoke-Step -FilePath $php -Arguments @('artisan', 'config:clear') -Log $Log | Out-Null

    $migrate = @('artisan', 'migrate', '--seed', '--force')
    if ($Fresh) { $migrate = @('artisan', 'migrate:fresh', '--seed', '--force') }

    Write-Log $Log 'Building the database...'
    if ((Invoke-Step -FilePath $php -Arguments $migrate -Log $Log) -ne 0) { return $false }

    if ($npm) {
        Write-Log $Log 'Compiling assets...'
        Invoke-Step -FilePath $npm -Arguments @('run', 'build') -Log $Log | Out-Null
    }

    return $true
}

# --- Server ---------------------------------------------------------------

function Start-AppServer {
    param([int]$Port = 8000, [scriptblock]$Log)

    $php = Find-Php
    if (-not $php) { Write-Log $Log 'PHP is missing.'; return $null }

    $proc = Start-Process -FilePath $php -WorkingDirectory (Get-ProjectRoot) `
        -ArgumentList @('artisan', 'serve', "--port=$Port") -PassThru -WindowStyle Hidden

    Write-Log $Log "Server started on http://127.0.0.1:$Port (pid $($proc.Id))."
    return $proc
}

function Stop-AppServer {
    param($Process, [scriptblock]$Log)

    if (-not $Process) { return }
    try {
        if (-not $Process.HasExited) {
            # artisan serve spawns a child php process; kill the tree.
            Invoke-Step -FilePath 'taskkill' -Arguments @('/PID', $Process.Id, '/T', '/F') -Log $Log | Out-Null
        }
        Write-Log $Log 'Server stopped.'
    } catch {
        Write-Log $Log "Could not stop the server: $($_.Exception.Message)"
    }
}
