<#
.SYNOPSIS
    Serves the WeCreate site locally without Docker, using PHP's built-in web server.

.DESCRIPTION
    Finds a PHP binary (preferring Laravel Herd, which ships one but does not put it
    on PATH), installs Composer dependencies if they are missing, then serves
    src/public through router.php.

.EXAMPLE
    .\scripts\serve.ps1
    .\scripts\serve.ps1 -Port 9000
    .\scripts\serve.ps1 -Install     # force a composer install first
#>

[CmdletBinding()]
param(
    [int]$Port = 8000,
    [string]$BindHost = '127.0.0.1',
    [switch]$Install
)

$ErrorActionPreference = 'Stop'

# Go to the project root (one level up from scripts/)
$Root = Split-Path -Parent $PSScriptRoot
$AppDir = Join-Path $Root 'src'

# --- Locate PHP -------------------------------------------------------------
# Herd installs PHP but does not add it to PATH, so check there first.
$HerdBin = Join-Path $env:USERPROFILE '.config\herd\bin'
$Php = $null

foreach ($candidate in @((Join-Path $HerdBin 'php.bat'), (Join-Path $HerdBin 'php83.bat'))) {
    if (Test-Path $candidate) { $Php = $candidate; break }
}

if (-not $Php) {
    $onPath = Get-Command php -ErrorAction SilentlyContinue
    if ($onPath) { $Php = $onPath.Source }
}

if (-not $Php) {
    throw "No PHP binary found. Install Laravel Herd, or put php.exe on your PATH."
}

Write-Host "PHP:  $Php" -ForegroundColor DarkGray
& $Php -v | Select-Object -First 1 | Write-Host -ForegroundColor DarkGray

# --- Install dependencies ---------------------------------------------------
$Autoload = Join-Path $AppDir 'vendor\autoload.php'

if ($Install -or -not (Test-Path $Autoload)) {
    # Prefer Herd's bundled composer.phar; the global shim breaks without php on PATH.
    $ComposerPhar = Join-Path $HerdBin 'composer.phar'

    Write-Host "`nInstalling Composer dependencies..." -ForegroundColor Cyan
    Push-Location $AppDir
    try {
        if (Test-Path $ComposerPhar) {
            & $Php $ComposerPhar install --no-interaction
        } else {
            & composer install --no-interaction
        }
        if ($LASTEXITCODE -ne 0) { throw "composer install failed (exit $LASTEXITCODE)" }
    } finally {
        Pop-Location
    }
}

# --- Serve ------------------------------------------------------------------
$DocRoot = Join-Path $AppDir 'public'
$Router = Join-Path $Root 'router.php'

if (-not (Test-Path $Router)) {
    throw "router.php not found at $Router. Without it, /about-us and /contact return 404."
}

Write-Host "`nWeCreate is running at http://${BindHost}:${Port}" -ForegroundColor Green
Write-Host "Note: the contact form needs mail(), which does not work here. Press Ctrl+C to stop.`n" -ForegroundColor DarkGray

Push-Location $Root
try {
    & $Php -S "${BindHost}:${Port}" -t $DocRoot $Router
} finally {
    Pop-Location
}
