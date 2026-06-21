# Пакує проєкт для завантаження на ADM (без .env, logs, git)
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
$outZip = Join-Path $root "clothstore-upload.zip"

Write-Host "Composer production install..."
Push-Location $root
& composer install --no-dev --optimize-autoloader --no-interaction
if ($LASTEXITCODE -ne 0) { Pop-Location; exit 1 }
Pop-Location

if (Test-Path $outZip) { Remove-Item $outZip -Force }

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::Open($outZip, [System.IO.Compression.ZipArchiveMode]::Create)

$excludePrefix = @(
    ".git\", "node_modules\", "tests\", ".cursor\",
    "storage\logs\", "storage\framework\cache\data\",
    "storage\framework\sessions\", "storage\framework\views\"
)
$excludeExact = @(".env", "clothstore-upload.zip", "phpunit.xml")

$count = 0
Get-ChildItem -Path $root -Recurse -File -Force | ForEach-Object {
    $rel = $_.FullName.Substring($root.Length + 1)
    foreach ($p in $excludePrefix) {
        if ($rel.StartsWith($p, [StringComparison]::OrdinalIgnoreCase)) { return }
    }
    if ($excludeExact -contains $rel) { return }

    $entry = $rel.Replace("\", "/")
    [void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $entry)
    $script:count++
}
$zip.Dispose()

$sizeMb = [math]::Round((Get-Item $outZip).Length / 1MB, 1)
Write-Host "OK: $outZip ($count files, ${sizeMb} MB)"
Write-Host "Next: upload ZIP to ADM, extract, set document root to public, run bash deploy/adm-setup.sh"
