# Deploy local changes to ADM via SCP + SSH
param(
    [ValidateSet('Changed', 'Quick', 'Assets', 'All', 'Files')]
    [string]$Mode = 'Changed',
    [string[]]$Files = @(),
    [switch]$TestConnection
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$configPath = Join-Path $PSScriptRoot 'deploy.config.json'
$examplePath = Join-Path $PSScriptRoot 'deploy.config.example.json'

if (-not (Test-Path $configPath)) {
    Copy-Item $examplePath $configPath
    Write-Host 'Created deploy/deploy.config.json - check paths and re-run.' -ForegroundColor Yellow
    exit 1
}

$config = Get-Content $configPath -Raw | ConvertFrom-Json
$sshTarget = $config.sshUser + '@' + $config.sshHost
$port = if ($config.sshPort) { [int]$config.sshPort } else { 22 }
$remoteHome = '/home/' + $config.sshUser
$remoteLaravel = ($remoteHome + '/' + $config.remoteLaravel).Replace('\', '/')
$remoteWww = ($remoteHome + '/' + $config.remoteWww).Replace('\', '/')

$keyPath = Join-Path $env:USERPROFILE '.ssh\adm_clothstore'
if ($config.sshKeyPath -and (Test-Path -LiteralPath $config.sshKeyPath)) {
    $keyPath = $config.sshKeyPath
}
if (-not (Test-Path -LiteralPath $keyPath)) {
    Write-Host ('SSH key not found: ' + $keyPath) -ForegroundColor Red
    Write-Host 'Run: powershell -File deploy\setup-ssh-key.ps1' -ForegroundColor Yellow
    exit 1
}

$sshOpts = @(
    '-p', $port,
    '-i', $keyPath,
    '-o', 'IdentitiesOnly=yes',
    '-o', 'PubkeyAuthentication=yes',
    '-o', 'PreferredAuthentications=publickey,password',
    '-o', 'ConnectTimeout=20',
    '-o', 'NumberOfPasswordPrompts=1'
)
$scpOpts = @(
    '-P', $port,
    '-i', $keyPath,
    '-o', 'IdentitiesOnly=yes',
    '-o', 'PubkeyAuthentication=yes',
    '-o', 'PreferredAuthentications=publickey,password',
    '-o', 'ConnectTimeout=20',
    '-o', 'NumberOfPasswordPrompts=1'
)

function Invoke-Ssh {
    param([string]$Command)
    & ssh @sshOpts $sshTarget $Command
    return $LASTEXITCODE
}

function Invoke-Scp {
    param(
        [string[]]$LocalFiles,
        [string]$RemoteDir
    )
    $dest = $sshTarget + ':' + $RemoteDir + '/'
    & scp @scpOpts -q @LocalFiles $dest
    return $LASTEXITCODE
}

function Test-SshConnection {
    Write-Host ('SSH key: ' + $keyPath) -ForegroundColor DarkGray
    Write-Host ('Target: ' + $sshTarget) -ForegroundColor DarkGray
    Write-Host 'Connecting (enter SSH password if asked)...' -ForegroundColor DarkGray
    $code = Invoke-Ssh 'echo ok'
    if ($code -ne 0) {
        Write-Host 'SSH failed.' -ForegroundColor Red
        Write-Host 'Run in THIS window (not powershell -File):' -ForegroundColor Yellow
        Write-Host ('  .\deploy\push.ps1 -Mode Files -Files "app/Http/Controllers/CheckoutController.php"') -ForegroundColor Yellow
        Write-Host 'Or test:' -ForegroundColor Yellow
        Write-Host ('  ssh -i "' + $keyPath + '" -o IdentitiesOnly=yes ' + $sshTarget) -ForegroundColor Yellow
        exit 1
    }
    Write-Host 'SSH connected.' -ForegroundColor Green
}

function Get-DeployFileList {
    param([string]$DeployMode)

    $list = New-Object System.Collections.Generic.List[string]

    switch ($DeployMode) {
        'Quick' {
            foreach ($dir in @('app', 'routes', 'resources', 'config', 'bootstrap', 'database')) {
                $full = Join-Path $root $dir
                if (Test-Path $full) {
                    Get-ChildItem $full -Recurse -File | ForEach-Object {
                        $rel = $_.FullName.Substring($root.Length + 1).Replace('\', '/')
                        $list.Add($rel)
                    }
                }
            }
        }
        'Assets' {
            $assets = Join-Path $root 'public/assets'
            if (Test-Path $assets) {
                Get-ChildItem $assets -Recurse -File | ForEach-Object {
                    $rel = $_.FullName.Substring($root.Length + 1).Replace('\', '/')
                    $list.Add($rel)
                }
            }
        }
        'All' {
            (Get-DeployFileList -DeployMode 'Quick') | ForEach-Object { $list.Add($_) }
            (Get-DeployFileList -DeployMode 'Assets') | ForEach-Object { $list.Add($_) }
        }
        'Changed' {
            Push-Location $root
            $changed = git status --porcelain 2>$null | ForEach-Object {
                if ($_ -match '^\?\?\s+(.+)$') { return $Matches[1].Trim() }
                if ($_ -match '^[ MADRCU?!]{2}\s+(.+)$') { return $Matches[1].Trim() }
            } | Where-Object { $_ }
            Pop-Location

            if (-not $changed) {
                Write-Host 'No git changes. Use -Mode Quick or -Files ...' -ForegroundColor Yellow
                exit 0
            }

            foreach ($item in $changed) {
                $item = $item.Replace('\', '/')
                if ($item -match '^(vendor|node_modules|storage|\.env|deploy/deploy\.config\.json)/') { continue }
                $full = Join-Path $root $item
                if (Test-Path $full -PathType Leaf) {
                    $list.Add($item)
                } elseif (Test-Path $full -PathType Container) {
                    Get-ChildItem $full -Recurse -File | ForEach-Object {
                        $rel = $_.FullName.Substring($root.Length + 1).Replace('\', '/')
                        $list.Add($rel)
                    }
                }
            }
        }
        'Files' {
            foreach ($f in $Files) {
                $f = $f.Replace('\', '/').TrimStart('/')
                $full = Join-Path $root $f
                if (-not (Test-Path $full)) {
                    Write-Host ('Not found: ' + $f) -ForegroundColor Red
                    exit 1
                }
                $list.Add($f)
            }
        }
    }

    return $list | Select-Object -Unique
}

function Get-RemotePath {
    param([string]$RelativePath)
    $RelativePath = $RelativePath.Replace('\', '/')

    if ($RelativePath.StartsWith('public/assets/')) {
        $sub = $RelativePath.Substring('public/'.Length)
        return ($remoteWww + '/' + $sub)
    }
    if ($RelativePath -eq 'public/index.php') {
        return ($remoteWww + '/index.php')
    }

    return ($remoteLaravel + '/' + $RelativePath)
}

Write-Host ('SSH key: ' + $keyPath) -ForegroundColor DarkGray
Write-Host ('Target: ' + $sshTarget) -ForegroundColor DarkGray
Write-Host 'Enter SSH password when asked (usually 1-2 times per deploy).' -ForegroundColor Yellow

if ($TestConnection) {
    Test-SshConnection
}

if ($Mode -eq 'Files' -and $Files.Count -eq 0) {
    Write-Host 'Use -Files path1,path2 with -Mode Files' -ForegroundColor Red
    exit 1
}

$deployMode = if ($Mode -eq 'Files') { 'Files' } else { $Mode }
$toUpload = @(Get-DeployFileList -DeployMode $deployMode)
if ($toUpload.Count -eq 0) {
    Write-Host 'Nothing to upload.'
    exit 0
}

Write-Host ('Uploading ' + $toUpload.Count + ' file(s) to ' + $sshTarget + ' ...') -ForegroundColor Cyan

$uploadGroups = @{}
foreach ($rel in $toUpload) {
    $local = Join-Path $root ($rel.Replace('/', '\'))
    $remote = Get-RemotePath -RelativePath $rel
    $remoteDir = ($remote -replace '/[^/]+$','')
    if (-not $uploadGroups.ContainsKey($remoteDir)) {
        $uploadGroups[$remoteDir] = New-Object System.Collections.Generic.List[object]
    }
    $uploadGroups[$remoteDir].Add([PSCustomObject]@{
        Relative = $rel
        Local = $local
        Remote = $remote
    })
}

$uniqueDirs = $uploadGroups.Keys | Sort-Object -Unique
if ($uniqueDirs.Count -gt 0) {
    $mkdirCmd = ($uniqueDirs | ForEach-Object { 'mkdir -p "' + $_ + '"' }) -join ' && '
    $code = Invoke-Ssh $mkdirCmd
    if ($code -ne 0) {
        Write-Host 'Failed to create remote folders.' -ForegroundColor Red
        exit 1
    }
}

foreach ($remoteDir in $uniqueDirs) {
    $items = $uploadGroups[$remoteDir]
    $localFiles = @($items | ForEach-Object { $_.Local })
    $code = Invoke-Scp -LocalFiles $localFiles -RemoteDir $remoteDir
    if ($code -ne 0) {
        Write-Host ('Failed upload to: ' + $remoteDir) -ForegroundColor Red
        exit 1
    }
    foreach ($item in $items) {
        Write-Host ('  OK ' + $item.Relative)
    }
}

Write-Host 'Running post-deploy on server...' -ForegroundColor Cyan
$postCmd = 'cd ~/clothstored.shop/clothstore-upload && php artisan view:clear && php artisan cache:clear && php artisan route:clear && rm -f bootstrap/cache/config.php bootstrap/cache/routes.php bootstrap/cache/services.php 2>/dev/null; chmod -R ug+rwx storage bootstrap/cache 2>/dev/null; echo Deploy OK'
$code = Invoke-Ssh $postCmd
if ($code -ne 0) {
    Write-Host 'post-deploy failed (files uploaded — clear cache manually in SSH)' -ForegroundColor Yellow
    Write-Host '  cd ~/clothstored.shop/clothstore-upload && php artisan view:clear && php artisan cache:clear' -ForegroundColor DarkGray
} else {
    Write-Host 'Post-deploy OK.' -ForegroundColor Green
}

Write-Host 'Done. Check: https://clothstored.shop' -ForegroundColor Green
