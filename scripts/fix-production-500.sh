#!/usr/bin/env bash
# Corrige erro 500 após deploy (cache/permissões no aaPanel).
set -euo pipefail

APP_DIR="${APP_DIR:-/www/wwwroot/precifique.tdesksolutions.com.br}"
PHP_BIN="${PHP_BIN:-/www/server/php/83/bin/php}"
APP_USER="${APP_USER:-www}"

echo "==> Corrigindo Precifique em ${APP_DIR}"

cd "$APP_DIR"

git pull origin main || true

chown -R "${APP_USER}:${APP_USER}" storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

"${PHP_BIN}" artisan optimize:clear
"${PHP_BIN}" artisan migrate --force
"${PHP_BIN}" scripts/generate-icons.php 2>/dev/null || true

chown -R "${APP_USER}:${APP_USER}" storage bootstrap/cache public/apple-touch-icon.png public/apple-touch-icon-precomposed.png public/images

"${PHP_BIN}" artisan config:cache
"${PHP_BIN}" artisan route:cache
"${PHP_BIN}" artisan view:cache

chown -R "${APP_USER}:${APP_USER}" storage bootstrap/cache

echo "==> Pronto. Teste: curl -I https://seudominio/login"
