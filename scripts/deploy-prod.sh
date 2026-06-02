#!/usr/bin/env bash
# Deploy produção — Linux/macOS
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [[ ! -f .env ]]; then
  echo "Crie .env a partir de .env.production.example e preencha as variáveis."
  exit 1
fi

if grep -qE '^APP_DEBUG=(true|1)' .env 2>/dev/null; then
  echo "ERRO: APP_DEBUG deve ser false em produção."
  exit 1
fi

echo "==> Build e subir stack Docker"
docker compose -f docker-compose.prod.yml up -d --build

echo "==> Aguardar MySQL..."
sleep 20

echo "==> Migrar (sem seed)"
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

echo "==> Storage link e caches"
docker compose -f docker-compose.prod.yml exec -T app php artisan storage:link --force 2>/dev/null || true
docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache

echo "==> Deploy concluído."
echo "    Health: curl -s https://seudominio/health"
echo "    Admin:  defina ADMIN_PASSWORD e rode: php artisan precifique:ensure-admin"
