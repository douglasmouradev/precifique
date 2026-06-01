# Deploy produção — Precifique
# Requisitos: Docker + Docker Compose v2

set -euo pipefail

echo "==> Build assets"
npm ci
npm run build

echo "==> Composer (produção)"
composer install --no-dev --optimize-autoloader

echo "==> Subir stack"
docker compose -f docker-compose.prod.yml up -d --build

echo "==> Aguardar MySQL..."
sleep 15

echo "==> Migrar e otimizar"
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

echo "==> Pronto. Acesse http://localhost (ou APP_PORT)"
