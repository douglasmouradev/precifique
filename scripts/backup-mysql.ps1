# Backup MySQL — produção (Windows)
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

$BackupDir = if ($env:BACKUP_DIR) { $env:BACKUP_DIR } else { Join-Path $Root "storage\backups" }
New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null

$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$File = Join-Path $BackupDir "precifique_$Timestamp.sql.gz"

$envContent = Get-Content ".env" -ErrorAction SilentlyContinue
$dbName = "precifique"
$dbPass = $null
foreach ($line in $envContent) {
    if ($line -match '^DB_DATABASE=(.+)$') { $dbName = $matches[1].Trim() }
    if ($line -match '^DB_PASSWORD=(.+)$') { $dbPass = $matches[1].Trim() }
}
if (-not $dbPass) { throw "Defina DB_PASSWORD no .env" }

Write-Host "==> Backup -> $File" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml exec -T mysql `
  mysqldump -u root -p"$dbPass" --single-transaction --routines $dbName |
  gzip > $File

Write-Host "==> Concluído." -ForegroundColor Green
Get-ChildItem $BackupDir -Filter "precifique_*.sql.gz" |
  Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-14) } |
  Remove-Item -Force
