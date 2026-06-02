# Deploy produção — Windows / PowerShell
$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

if (-not (Test-Path ".env")) {
    Write-Error "Crie .env a partir de .env.production.example e preencha as variáveis."
}

if (Select-String -Path ".env" -Pattern '^APP_DEBUG=(true|1)' -Quiet) {
    Write-Error "APP_DEBUG deve ser false em produção."
}

Write-Host "==> Build e subir stack Docker" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml up -d --build

Write-Host "==> Aguardar MySQL..." -ForegroundColor Cyan
Start-Sleep -Seconds 20

Write-Host "==> Migrar (sem seed)" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

Write-Host "==> Storage link e caches" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link --force 2>$null
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

Write-Host "==> Deploy concluído." -ForegroundColor Green
Write-Host "    Admin: defina ADMIN_PASSWORD no .env e rode precifique:ensure-admin no container app."
