# One-time: SSH key for deploy without password (run on your PC)
$ErrorActionPreference = 'Stop'

$configPath = Join-Path $PSScriptRoot 'deploy.config.json'
$examplePath = Join-Path $PSScriptRoot 'deploy.config.example.json'
if (-not (Test-Path $configPath)) { Copy-Item $examplePath $configPath }

$config = Get-Content $configPath -Raw | ConvertFrom-Json
$keyDir = Join-Path $env:USERPROFILE '.ssh'
$keyFile = Join-Path $keyDir 'adm_clothstore'
$sshTarget = "{0}@{1}" -f $config.sshUser, $config.sshHost

if (-not (Test-Path $keyDir)) { New-Item -ItemType Directory -Path $keyDir | Out-Null }

if (-not (Test-Path $keyFile)) {
    Write-Host "Creating SSH key: $keyFile"
    ssh-keygen -t ed25519 -f $keyFile -N '""' -C "clothstore-deploy"
}

$sshBase = @(
    '-i', $keyFile,
    '-o', 'IdentitiesOnly=yes',
    '-o', 'PreferredAuthentications=publickey,password',
    '-o', 'PubkeyAuthentication=yes',
    '-o', 'ConnectTimeout=20',
    '-o', 'NumberOfPasswordPrompts=1'
)

Write-Host ""
Write-Host "=== Step 1: Add this public key in ADM panel ===" -ForegroundColor Cyan
Write-Host "ADM -> Hosting -> bg622152 -> SSH -> Authorized keys (paste ONE line, save)"
Write-Host ""
Get-Content "$keyFile.pub"
Write-Host ""
Read-Host "Press Enter AFTER you saved the key in ADM"

Write-Host ""
Write-Host "=== Step 2: Test connection ===" -ForegroundColor Cyan
Write-Host "If asked for password — enter SSH password from ADM (only if key not yet active)."
Write-Host ""

& ssh @sshBase $sshTarget "echo SSH key works"

if ($LASTEXITCODE -eq 0) {
    $json = Get-Content $configPath -Raw | ConvertFrom-Json
    $json.sshKeyPath = $keyFile.Replace('\', '/')
    $json | ConvertTo-Json | Set-Content $configPath -Encoding UTF8
    Write-Host ""
    Write-Host "Saved key path to deploy.config.json" -ForegroundColor Green
    Write-Host "Deploy: .\deploy\push.ps1 -Mode Changed"
} else {
    Write-Host "Failed. Add the public key in ADM first, then run this script again." -ForegroundColor Red
}
