#!/usr/bin/env bash
# Deploy Precifique em VPS Ubuntu (PHP 8.3 + Nginx)
set -euo pipefail

APP_DIR="${APP_DIR:-/www/wwwroot/precifique.tdesksolutions.com.br}"
APP_USER="${APP_USER:-www}"

echo "==> Precifique VPS deploy"

if [[ $EUID -eq 0 ]]; then
  echo "AVISO: evite rodar como root. Use um usuario deploy com sudo."
fi

command -v php >/dev/null || { echo "Instale PHP 8.3: apt install php8.3-fpm php8.3-mysql php8.3-redis php8.3-gd php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip"; exit 1; }
command -v composer >/dev/null || { echo "Instale Composer 2.7+: https://getcomposer.org/download/"; exit 1; }
command -v npm >/dev/null || { echo "Instale Node.js 20+: apt install nodejs npm"; exit 1; }

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo "PHP: $PHP_VER"
composer --version

cd "$APP_DIR"

if [[ ! -f .env ]]; then
  cp .env.vps.example .env
  echo "Edite .env (DB_HOST=127.0.0.1, ADMIN_PASSWORD, HEALTH_CHECK_TOKEN) antes de continuar."
  exit 1
fi

composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
  php artisan key:generate --force
else
  echo "APP_KEY já definida — mantendo chave existente."
fi
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true
php artisan precifique:ensure-plans
php artisan precifique:ensure-admin
php artisan precifique:preflight
php scripts/generate-icons.php 2>/dev/null || true

chown -R "$APP_USER:$APP_USER" storage bootstrap/cache public/apple-touch-icon.png public/apple-touch-icon-precomposed.png public/images 2>/dev/null || true
chmod -R 775 storage bootstrap/cache

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R "$APP_USER:$APP_USER" storage bootstrap/cache

echo "==> Deploy concluído. Configure Nginx + Supervisor (veja docs/VPS.md)"
