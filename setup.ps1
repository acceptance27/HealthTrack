<#
.SYNOPSIS
Bootstrap the HealthTrack application locally on Windows.
.DESCRIPTION
This script prepares the project by installing dependencies, copying .env, generating an app key,
optionally creating the MySQL database if the client is available, running migrations and seeders,
and building frontend assets.
.PARAMETER Serve
If specified, starts the Laravel development server after setup.
#>
param(
    [switch]$Serve
)

function Write-Info($message) {
    Write-Host "[INFO] $message" -ForegroundColor Cyan
}

function Write-Warn($message) {
    Write-Host "[WARN] $message" -ForegroundColor Yellow
}

function Write-ErrorAndExit($message) {
    Write-Host "[ERROR] $message" -ForegroundColor Red
    exit 1
}

function Test-CommandExists($command) {
    return (Get-Command $command -ErrorAction SilentlyContinue) -ne $null
}

function Run-Command($fullCommand) {
    Write-Info "Running: $fullCommand"

    $startInfo = New-Object System.Diagnostics.ProcessStartInfo
    $startInfo.FileName = 'cmd'
    $startInfo.Arguments = "/c $fullCommand"
    $startInfo.RedirectStandardOutput = $true
    $startInfo.RedirectStandardError = $true
    $startInfo.UseShellExecute = $false
    $startInfo.CreateNoWindow = $true

    try {
        $process = [System.Diagnostics.Process]::Start($startInfo)
    } catch {
        Write-ErrorAndExit("Failed to start command: $fullCommand`n$($_.Exception.Message)")
    }

    $output = $process.StandardOutput.ReadToEnd()
    $errorOutput = $process.StandardError.ReadToEnd()
    $process.WaitForExit()

    if ($output) {
        Write-Host $output
    }

    if ($process.ExitCode -ne 0) {
        Write-ErrorAndExit("Command failed: $fullCommand`n$errorOutput")
    }
}

function Read-EnvFile($path) {
    $result = @{}
    Get-Content $path | ForEach-Object {
        if ($_ -and $_ -notmatch '^[\s#]') {
            if ($_ -match '^(?<key>[^=]+)=(?<value>.*)$') {
                $key = $matches['key'].Trim()
                $value = $matches['value'].Trim('"')
                $result[$key] = $value
            }
        }
    }
    return $result
}

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $projectRoot
Write-Info "Project root: $projectRoot"

$requiredCommands = @('php', 'composer', 'node', 'npm')
foreach ($command in $requiredCommands) {
    if (-not (Test-CommandExists $command)) {
        Write-ErrorAndExit("Required command '$command' is not installed or not on PATH. Please install it and rerun this script.")
    }
}

if (-not (Test-Path '.env')) {
    if (-not (Test-Path '.env.example')) {
        Write-ErrorAndExit('.env.example not found. Cannot create .env.')
    }

    Copy-Item '.env.example' '.env'
    Write-Info 'Created .env from .env.example'
}

$envData = Read-EnvFile '.env'
if (-not $envData.ContainsKey('APP_KEY') -or [string]::IsNullOrWhiteSpace($envData['APP_KEY'])) {
    Run-Command 'php artisan key:generate'
} else {
    Write-Info 'APP_KEY already set.'
}

Run-Command 'composer install --no-interaction --prefer-dist'
Run-Command 'npm install'
Run-Command 'npm run build'

$dbHost = $envData['DB_HOST']
$dbPort = $envData['DB_PORT']
$dbName = $envData['DB_DATABASE']
$dbUser = $envData['DB_USERNAME']
$dbPassword = $envData['DB_PASSWORD']

if ($dbHost -and $dbName -and $dbUser) {
    if (Test-CommandExists 'mysql') {
        Write-Info "Attempting to create database '$dbName' if it does not exist."
        $mysqlArgs = @()
        $mysqlArgs += "--host=$dbHost"
        $mysqlArgs += "--port=$dbPort"
        $mysqlArgs += "--user=$dbUser"
        if ($dbPassword -ne $null -and $dbPassword -ne '') {
            $mysqlArgs += "--password=$dbPassword"
        }
        $mysqlArgs += "--execute=CREATE DATABASE IF NOT EXISTS \`$dbName\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

        try {
            Start-Process -FilePath 'mysql' -ArgumentList $mysqlArgs -NoNewWindow -Wait -PassThru -ErrorAction Stop | Out-Null
            Write-Info "Database check/creation complete."
        } catch {
            Write-Warn "Could not create database using mysql client. Please confirm MySQL is running and credentials are correct."
        }
    } else {
        Write-Warn 'MySQL client not found. Skipping automatic database creation. Ensure MySQL is installed and running before migrating.'
    }
} else {
    Write-Warn 'Database configuration is incomplete in .env. Skipping automatic database creation.'
}

Write-Info 'Running migrations and seeders.'
Run-Command 'php artisan migrate:fresh --seed --force'

Write-Info 'Setup complete.'
if ($Serve) {
    Write-Info 'Starting Laravel development server...'
    php artisan serve --host=127.0.0.1 --port=8000
} else {
    Write-Info 'You can start the server with: php artisan serve --host=127.0.0.1 --port=8000'
}
