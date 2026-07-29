# HealthTrack setup window.
#
# A thin WinForms front-end over lib.ps1. All the actual work lives there; this
# file only arranges controls and wires them to functions.
#
# Launch it by double-clicking Setup.bat in the project root.

Set-StrictMode -Version 2.0
Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

. (Join-Path $PSScriptRoot 'lib.ps1')

[System.Windows.Forms.Application]::EnableVisualStyles()

$script:ServerProcess = $null

# --- Palette, loosely matching the app itself ----------------------------

$ink       = [System.Drawing.Color]::FromArgb(31, 36, 33)
$brand     = [System.Drawing.Color]::FromArgb(15, 107, 95)
$parchment = [System.Drawing.Color]::FromArgb(246, 241, 232)
$ok        = [System.Drawing.Color]::FromArgb(21, 115, 71)
$missing   = [System.Drawing.Color]::FromArgb(178, 69, 62)

$fontBody   = New-Object System.Drawing.Font('Segoe UI', 9)
$fontHead   = New-Object System.Drawing.Font('Segoe UI', 15, [System.Drawing.FontStyle]::Bold)
$fontSmall  = New-Object System.Drawing.Font('Segoe UI', 8)
$fontMono   = New-Object System.Drawing.Font('Consolas', 9)

# --- Form -----------------------------------------------------------------

$form = New-Object System.Windows.Forms.Form
$form.Text = 'HealthTrack Setup'
$form.Size = New-Object System.Drawing.Size(900, 800)
$form.StartPosition = 'CenterScreen'
$form.BackColor = $parchment
$form.Font = $fontBody

$title = New-Object System.Windows.Forms.Label
$title.Text = 'HealthTrack Setup'
$title.Font = $fontHead
$title.ForeColor = $brand
$title.Location = New-Object System.Drawing.Point(20, 16)
$title.Size = New-Object System.Drawing.Size(500, 30)
$form.Controls.Add($title)

$subtitle = New-Object System.Windows.Forms.Label
$subtitle.Text = 'Work down the numbered buttons. Each one is safe to run again.'
$subtitle.ForeColor = $ink
$subtitle.Location = New-Object System.Drawing.Point(22, 46)
$subtitle.Size = New-Object System.Drawing.Size(600, 20)
$form.Controls.Add($subtitle)

# --- Status ---------------------------------------------------------------

$statusBox = New-Object System.Windows.Forms.GroupBox
$statusBox.Text = 'Status'
$statusBox.Location = New-Object System.Drawing.Point(20, 76)
$statusBox.Size = New-Object System.Drawing.Size(840, 130)
$statusBox.BackColor = $parchment
$form.Controls.Add($statusBox)

$toolsLabel = New-Object System.Windows.Forms.Label
$toolsLabel.Location = New-Object System.Drawing.Point(16, 24)
$toolsLabel.Size = New-Object System.Drawing.Size(380, 100)
$toolsLabel.Font = $fontSmall
$statusBox.Controls.Add($toolsLabel)

$projectLabel = New-Object System.Windows.Forms.Label
$projectLabel.Location = New-Object System.Drawing.Point(420, 24)
$projectLabel.Size = New-Object System.Drawing.Size(300, 100)
$projectLabel.Font = $fontSmall
$statusBox.Controls.Add($projectLabel)

$recheck = New-Object System.Windows.Forms.Button
$recheck.Text = 'Re-check'
$recheck.Location = New-Object System.Drawing.Point(735, 92)
$recheck.Size = New-Object System.Drawing.Size(90, 26)
$statusBox.Controls.Add($recheck)

# --- Database -------------------------------------------------------------

$dbBox = New-Object System.Windows.Forms.GroupBox
$dbBox.Text = 'Database'
$dbBox.Location = New-Object System.Drawing.Point(20, 216)
$dbBox.Size = New-Object System.Drawing.Size(840, 170)
$dbBox.BackColor = $parchment
$form.Controls.Add($dbBox)

# PostgreSQL is listed first and selected by default: it is the database the
# study specifies and the one this project actually targets. SQLite is kept
# only as a fallback for getting the site running without a database server.
$radioPgsql = New-Object System.Windows.Forms.RadioButton
$radioPgsql.Text = 'PostgreSQL  (recommended -- what the study specifies)'
$radioPgsql.Location = New-Object System.Drawing.Point(16, 24)
$radioPgsql.Size = New-Object System.Drawing.Size(340, 22)
$radioPgsql.Checked = $true
$dbBox.Controls.Add($radioPgsql)

$radioSqlite = New-Object System.Windows.Forms.RadioButton
$radioSqlite.Text = 'SQLite  (fallback -- a single file, nothing to install)'
$radioSqlite.Location = New-Object System.Drawing.Point(380, 24)
$radioSqlite.Size = New-Object System.Drawing.Size(340, 22)
$dbBox.Controls.Add($radioSqlite)

function New-LabelledField {
    param([string]$Text, [int]$X, [int]$Y, [string]$Default, [int]$Width = 150, [switch]$Secret)

    $l = New-Object System.Windows.Forms.Label
    $l.Text = $Text
    $l.Location = New-Object System.Drawing.Point($X, $Y)
    $l.Size = New-Object System.Drawing.Size(90, 20)
    $dbBox.Controls.Add($l)

    $t = New-Object System.Windows.Forms.TextBox
    $t.Location = New-Object System.Drawing.Point(($X + 92), ($Y - 3))
    $t.Size = New-Object System.Drawing.Size($Width, 22)
    $t.Text = $Default
    if ($Secret) { $t.UseSystemPasswordChar = $true }
    $dbBox.Controls.Add($t)
    return $t
}

$fHost     = New-LabelledField -Text 'Host'     -X 16  -Y 62  -Default '127.0.0.1'
$fPort     = New-LabelledField -Text 'Port'     -X 16  -Y 94  -Default '5432' -Width 80
$fDatabase = New-LabelledField -Text 'Database' -X 16  -Y 126 -Default 'healthtrack'
$fUser     = New-LabelledField -Text 'Username' -X 420 -Y 62  -Default 'postgres'
$fPassword = New-LabelledField -Text 'Password' -X 420 -Y 94  -Default '' -Secret

$pwNote = New-Object System.Windows.Forms.Label
$pwNote.Text = 'The password you chose when installing PostgreSQL. It is written only to .env.'
$pwNote.Location = New-Object System.Drawing.Point(422, 124)
$pwNote.Size = New-Object System.Drawing.Size(400, 32)
$pwNote.Font = $fontSmall
$dbBox.Controls.Add($pwNote)

# --- Actions --------------------------------------------------------------

$actions = New-Object System.Windows.Forms.GroupBox
$actions.Text = 'Actions'
$actions.Location = New-Object System.Drawing.Point(20, 396)
$actions.Size = New-Object System.Drawing.Size(840, 96)
$actions.BackColor = $parchment
$form.Controls.Add($actions)

function New-ActionButton {
    param([string]$Text, [int]$X, [int]$Y, [int]$Width = 195)
    $b = New-Object System.Windows.Forms.Button
    $b.Text = $Text
    $b.Location = New-Object System.Drawing.Point($X, $Y)
    $b.Size = New-Object System.Drawing.Size($Width, 32)
    $actions.Controls.Add($b)
    return $b
}

$btnPrereq  = New-ActionButton -Text '1. Install prerequisites' -X 16  -Y 26
$btnDeps    = New-ActionButton -Text '2. Install dependencies'  -X 223 -Y 26
$btnDb      = New-ActionButton -Text '3. Set up database'       -X 430 -Y 26
$btnStart   = New-ActionButton -Text '4. Start server'          -X 637 -Y 26 -Width 187

$btnStop    = New-ActionButton -Text 'Stop server'   -X 637 -Y 62 -Width 90
$btnStop.Enabled = $false
$btnOpen    = New-ActionButton -Text 'Open browser'  -X 733 -Y 62 -Width 91
$btnReset   = New-ActionButton -Text 'Reset database (deletes all data)' -X 16 -Y 62 -Width 250

# --- Log ------------------------------------------------------------------

# Named $logBox, not $log, and always addressed as $script:logBox below.
#
# PowerShell variable names are case-insensitive, so a control called $log is
# the same variable as the [scriptblock]$Log parameter that lib.ps1's Write-Log
# declares. The callback runs inside Write-Log's scope, so $log resolved to
# that parameter -- the scriptblock itself -- and every button died with
# "ScriptBlock does not contain a method named AppendText".
$logBox = New-Object System.Windows.Forms.TextBox
$logBox.Multiline = $true
$logBox.ScrollBars = 'Vertical'
$logBox.ReadOnly = $true
$logBox.Font = $fontMono
$logBox.BackColor = [System.Drawing.Color]::White
$logBox.Location = New-Object System.Drawing.Point(20, 502)
$logBox.Size = New-Object System.Drawing.Size(840, 240)
$form.Controls.Add($logBox)

# The callback every lib.ps1 function writes through. DoEvents keeps the
# window responsive during long steps without needing a background runspace.
# The $script: qualifier makes the lookup immune to any caller's locals.
$logger = {
    param([string]$message)
    $script:logBox.AppendText($message + "`r`n")
    $script:logBox.SelectionStart = $script:logBox.TextLength
    $script:logBox.ScrollToCaret()
    [System.Windows.Forms.Application]::DoEvents()
}

# --- Behaviour ------------------------------------------------------------

function Update-Status {
    $lines = @()
    foreach ($p in (Get-Prerequisites)) {
        $mark = 'missing'
        if ($p.Found) { $mark = 'installed' }
        $suffix = ''
        if (-not $p.Found -and -not $p.Required) { $suffix = '  (only needed for PostgreSQL)' }
        $lines += "{0,-14} {1}{2}" -f $p.Name, $mark, $suffix
    }
    $toolsLabel.Text = ($lines -join "`r`n")

    $plines = @()
    foreach ($s in (Get-ProjectStatus)) {
        $mark = 'no'
        if ($s.Found) { $mark = 'yes' }
        $plines += "{0,-26} {1}" -f $s.Name, $mark
    }
    $projectLabel.Text = ($plines -join "`r`n")
}

function Set-Busy {
    param([bool]$Busy)
    foreach ($b in @($btnPrereq, $btnDeps, $btnDb, $btnStart, $btnReset, $recheck)) {
        $b.Enabled = -not $Busy
    }
    $cursor = 'Default'
    if ($Busy) { $cursor = 'WaitCursor' }
    $form.Cursor = $cursor
    [System.Windows.Forms.Application]::DoEvents()
}

function Get-DatabaseChoice {
    if ($radioPgsql.Checked) { return 'pgsql' }
    return 'sqlite'
}

$recheck.Add_Click({ Update-Status })

$btnPrereq.Add_Click({
    Set-Busy $true
    & $logger '--- Installing prerequisites ---'
    Install-Php      -Log $logger | Out-Null
    Install-Composer -Log $logger | Out-Null
    Install-Node     -Log $logger | Out-Null
    if ($radioPgsql.Checked) { Install-Postgres -Log $logger | Out-Null }
    & $logger 'Done.'
    Update-Status
    Set-Busy $false
})

$btnDeps.Add_Click({
    Set-Busy $true
    & $logger '--- Installing dependencies ---'
    if (Install-Dependencies -Log $logger) { & $logger 'Done.' } else { & $logger 'Failed -- see above.' }
    Update-Status
    Set-Busy $false
})

$btnDb.Add_Click({
    Set-Busy $true
    & $logger '--- Setting up the database ---'

    $choice = Get-DatabaseChoice
    $proceed = $true

    if ($choice -eq 'pgsql') {
        if (-not $fPassword.Text) {
            [System.Windows.Forms.MessageBox]::Show(
                'Enter the PostgreSQL password you chose during installation.',
                'Password required', 'OK', 'Warning') | Out-Null
            $proceed = $false
        } else {
            $proceed = New-PostgresDatabase -DbHost $fHost.Text -Port $fPort.Text `
                -Database $fDatabase.Text -Username $fUser.Text -Password $fPassword.Text -Log $logger
        }
    }

    if ($proceed) {
        $written = Write-DatabaseConfig -Connection $choice -DbHost $fHost.Text -Port $fPort.Text `
            -Database $fDatabase.Text -Username $fUser.Text -Password $fPassword.Text -Log $logger
        if ($written) {
            if (Initialize-Application -Log $logger) { & $logger 'Done.' } else { & $logger 'Failed -- see above.' }
        }
    }

    Update-Status
    Set-Busy $false
})

$btnReset.Add_Click({
    $answer = [System.Windows.Forms.MessageBox]::Show(
        "This drops every table and reloads the demo data.`r`n`r`nAll existing records will be lost. Continue?",
        'Reset database', 'YesNo', 'Warning')
    if ($answer -ne 'Yes') { return }

    Set-Busy $true
    & $logger '--- Resetting the database ---'
    if (Initialize-Application -Fresh -Log $logger) { & $logger 'Done.' } else { & $logger 'Failed -- see above.' }
    Update-Status
    Set-Busy $false
})

$btnStart.Add_Click({
    if ($script:ServerProcess -and -not $script:ServerProcess.HasExited) {
        & $logger 'Server is already running.'
        return
    }
    $script:ServerProcess = Start-AppServer -Port 8000 -Log $logger
    if ($script:ServerProcess) {
        $btnStop.Enabled = $true
        $btnStart.Enabled = $false
        Start-Sleep -Milliseconds 900
        Start-Process 'http://127.0.0.1:8000'
    }
})

$btnStop.Add_Click({
    Stop-AppServer -Process $script:ServerProcess -Log $logger
    $script:ServerProcess = $null
    $btnStop.Enabled = $false
    $btnStart.Enabled = $true
})

$btnOpen.Add_Click({ Start-Process 'http://127.0.0.1:8000' })

# Leaving the server running after the window closes would orphan it.
$form.Add_FormClosing({
    if ($script:ServerProcess -and -not $script:ServerProcess.HasExited) {
        Stop-AppServer -Process $script:ServerProcess -Log $logger
    }
})

# Only the PostgreSQL fields are meaningful when PostgreSQL is selected.
$toggleDbFields = {
    $on = $radioPgsql.Checked
    foreach ($c in @($fHost, $fPort, $fDatabase, $fUser, $fPassword)) { $c.Enabled = $on }
    $pwNote.Enabled = $on
}
$radioSqlite.Add_CheckedChanged($toggleDbFields)
$radioPgsql.Add_CheckedChanged($toggleDbFields)
& $toggleDbFields

Update-Status
& $logger "Project: $(Get-ProjectRoot)"
& $logger 'Ready.'

[void]$form.ShowDialog()
