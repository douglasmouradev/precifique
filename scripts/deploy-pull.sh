#!/usr/bin/env bash
# Deploy rápido na VPS após git push (pull + build + cache)
set -euo pipefail

APP_DIR="${APP_DIR:-/www/wwwroot/precifique.tdesksolutions.com.br}"
PHP_BIN="${PHP_BIN:-/www/server/php/83/bin/php}"
NPM_BIN="${NPM_BIN:-npm}"

if [[ ! -d "$APP_DIR/.git" ]]; then
  echo "Diretório inválido: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

echo "==> git pull origin main"
git pull origin main

if [[ -f package.json ]]; then
  echo "==> npm ci && npm run build"
  $NPM_BIN ci
  $NPM_BIN run build
fi

echo "==> artisan caches"
$PHP_BIN artisan view:clear
$PHP_BIN artisan view:cache
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache

echo "==> Deploy pull concluído em $(date -Iseconds)"
