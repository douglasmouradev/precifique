#!/usr/bin/env bash
# Backup MySQL do stack de produção
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

BACKUP_DIR="${BACKUP_DIR:-$ROOT/storage/backups}"
mkdir -p "$BACKUP_DIR"

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
FILE="$BACKUP_DIR/precifique_${TIMESTAMP}.sql.gz"

source .env 2>/dev/null || true
DB_NAME="${DB_DATABASE:-precifique}"
DB_PASS="${DB_PASSWORD:?Defina DB_PASSWORD no .env}"

echo "==> Backup -> $FILE"
docker compose -f docker-compose.prod.yml exec -T mysql \
  mysqldump -u root -p"${DB_PASS}" --single-transaction --routines "$DB_NAME" \
  | gzip > "$FILE"

echo "==> OK ($(du -h "$FILE" | cut -f1))"
find "$BACKUP_DIR" -name 'precifique_*.sql.gz' -mtime +14 -delete 2>/dev/null || true
