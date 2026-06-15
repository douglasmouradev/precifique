# Precifique - setup rapido para desenvolvimento local (Windows)
$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

Write-Host "==> Precifique dev setup" -ForegroundColor Cyan

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Criado .env a partir de .env.example"
}

# SQLite para dev local (sem MySQL/Redis)
$envPath = Join-Path $root ".env"
$envContent = Get-Content $envPath -Raw
if ($envContent -notmatch '(?m)^DB_CONNECTION=sqlite') {
    $envContent = $envContent -replace '(?m)^DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite'
    $envContent = $envContent -replace '(?m)^SESSION_DRIVER=.*', 'SESSION_DRIVER=file'
    $envContent = $envContent -replace '(?m)^CACHE_STORE=.*', 'CACHE_STORE=file'
    $envContent = $envContent -replace '(?m)^QUEUE_CONNECTION=.*', 'QUEUE_CONNECTION=sync'
    if ($envContent -notmatch '(?m)^APP_LOCALE=pt_BR') {
        $envContent = $envContent -replace '(?m)^APP_LOCALE=.*', 'APP_LOCALE=pt_BR'
        $envContent = $envContent -replace '(?m)^APP_FALLBACK_LOCALE=.*', 'APP_FALLBACK_LOCALE=pt_BR'
    }
    Set-Content -Path $envPath -Value $envContent -NoNewline
    Write-Host "Ajustado .env para SQLite (dev local)"
}

if (-not (Test-Path "database\database.sqlite")) {
    New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null
    Write-Host "Criado database\database.sqlite"
}

php artisan key:generate --force
php artisan migrate --seed --force
if (-not (Test-Path "public\storage")) {
    php artisan storage:link
}
php artisan precifique:ensure-admin
php artisan precifique:ensure-demo

if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm install
    npm run build
} else {
    Write-Host "npm nao encontrado - pule o build de assets por enquanto." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Pronto!" -ForegroundColor Green
Write-Host '  Admin:  http://127.0.0.1:8000/login  (admin@precifique.com.br / Precifique@2026)'
Write-Host '  Demo:   http://127.0.0.1:8000/entrar  (demo@precifique.com.br / demo1234)'
Write-Host '  Rode:   php artisan serve'
