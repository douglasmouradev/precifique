#!/usr/bin/env bash
# Backup MySQL na VPS (aaPanel / PHP nativo, sem Docker)
set -euo pipefail

APP_DIR="${APP_DIR:-/www/wwwroot/precifique.tdesksolutions.com.br}"
BACKUP_DIR="${BACKUP_DIR:-$APP_DIR/storage/backups}"
PHP_BIN="${PHP_BIN:-/www/server/php/83/bin/php}"

mkdir -p "$BACKUP_DIR"

if [[ ! -f "$APP_DIR/.env" ]]; then
  echo "ERRO: .env não encontrado em $APP_DIR"
  exit 1
fi

# shellcheck disable=SC1091
source "$APP_DIR/.env"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USERNAME:?Defina DB_USERNAME no .env}"
DB_NAME="${DB_DATABASE:?Defina DB_DATABASE no .env}"
DB_PASS="${DB_PASSWORD:?Defina DB_PASSWORD no .env}"

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
FILE="$BACKUP_DIR/precifique_${TIMESTAMP}.sql.gz"

echo "==> Backup MySQL -> $FILE"
MYSQL_PWD="$DB_PASS" mysqldump \
  -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" \
  --single-transaction --routines --triggers "$DB_NAME" \
  | gzip > "$FILE"

echo "==> OK ($(du -h "$FILE" | cut -f1))"
find "$BACKUP_DIR" -name 'precifique_*.sql.gz' -mtime +14 -delete 2>/dev/null || true
