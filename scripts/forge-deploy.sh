#!/usr/bin/env bash
# Laravel Forge / sunucu dağıtımı için örnek betik.
# Forge panelinde genelde aynı adımlar satır satır yapıştırılır; bu dosya referans içindir.
set -euo pipefail

ROOT="${FORGE_SITE_PATH:-$(cd "$(dirname "$0")/.." && pwd)}"
cd "$ROOT"

if [[ -n "${FORGE_COMPOSER:-}" ]]; then
  "$FORGE_COMPOSER" install --no-dev --prefer-dist --no-interaction --optimize-autoloader
else
  composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
fi

if [[ -f package-lock.json ]]; then
  npm ci --omit=dev
else
  npm install --omit=dev
fi
npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

php artisan queue:restart || true
