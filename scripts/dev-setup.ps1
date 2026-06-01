# Precifique — setup rápido para desenvolvimento local (Windows)
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "==> Precifique dev setup" -ForegroundColor Cyan

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Criado .env a partir de .env.example"
}

if (-not (Test-Path "database\database.sqlite")) {
    New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null
    Write-Host "Criado database\database.sqlite"
}

php artisan key:generate --force
php artisan migrate --seed --force
php artisan storage:link 2>$null
php artisan precifique:ensure-admin
php artisan precifique:ensure-demo

if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm install
    npm run build
} else {
    Write-Host "npm não encontrado — pule o build de assets por enquanto." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Pronto!" -ForegroundColor Green
Write-Host "  Admin:  http://127.0.0.1:8000/login  (admin@precifique.com.br / Precifique@2026)"
Write-Host "  Demo:   http://127.0.0.1:8000/entrar  (demo@precifique.com.br / demo1234)"
Write-Host "  Rode:   php artisan serve"
