# Deploy produção — Precifique (Windows / PowerShell)

$ErrorActionPreference = "Stop"

Write-Host "==> Build assets" -ForegroundColor Cyan
npm install
npm run build

Write-Host "==> Composer (produção)" -ForegroundColor Cyan
php composer.phar install --no-dev --optimize-autoloader

Write-Host "==> Subir stack Docker" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml up -d --build

Write-Host "==> Aguardar MySQL..." -ForegroundColor Cyan
Start-Sleep -Seconds 15

Write-Host "==> Migrar e otimizar" -ForegroundColor Cyan
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

Write-Host "==> Deploy concluído." -ForegroundColor Green
