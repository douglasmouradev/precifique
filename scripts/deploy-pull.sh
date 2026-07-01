#!/usr/bin/env bash
# Deploy rápido na VPS após git push (pull + dependências + migrate + build + cache)
set -euo pipefail

APP_DIR="${APP_DIR:-/www/wwwroot/precifique.tdesksolutions.com.br}"
PHP_BIN="${PHP_BIN:-/www/server/php/83/bin/php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
APP_USER="${APP_USER:-www}"

if [[ ! -d "$APP_DIR/.git" ]]; then
  echo "Diretório inválido: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

echo "==> git fetch origin main"
git fetch origin main

echo "==> git reset --hard origin/main"
git reset --hard origin/main

if [[ -f composer.json ]]; then
  echo "==> composer install --no-dev"
  $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
fi

if [[ -f package.json ]]; then
  echo "==> npm install && npm run build"
  if [[ -f package-lock.json ]]; then
    $NPM_BIN ci || $NPM_BIN install
  else
    $NPM_BIN install
  fi
  $NPM_BIN run build
fi

echo "==> artisan migrate"
$PHP_BIN artisan migrate --force

echo "==> artisan caches"
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

if id "$APP_USER" &>/dev/null; then
  chown -R "$APP_USER:$APP_USER" storage bootstrap/cache 2>/dev/null || true
  chmod -R 775 storage bootstrap/cache 2>/dev/null || true
fi

echo "==> Deploy pull concluído em $(date -Iseconds)"
echo "    Reinicie o queue worker se houver mudança em jobs/listeners (Supervisor)."
