#!/usr/bin/env bash
# Laravel Forge — her dağıtımda çalıştırın (Deploy Script alanına yapıştırabilir veya: bash scripts/forge-deploy.sh)
set -euo pipefail

ROOT="${FORGE_SITE_PATH:-$(cd "$(dirname "$0")/.." && pwd)}"
cd "$ROOT"

echo "==> Deploy: $ROOT"

if [[ -n "${FORGE_SITE_BRANCH:-}" ]] && git rev-parse --is-inside-work-tree &>/dev/null; then
  git pull origin "$FORGE_SITE_BRANCH"
fi

php artisan down --refresh=15 --retry=60 --secret="${DEPLOY_SECRET:-forge-deploy}" 2>/dev/null || true

if [[ -n "${FORGE_COMPOSER:-}" ]]; then
  "$FORGE_COMPOSER" install --no-dev --prefer-dist --no-interaction --optimize-autoloader
else
  composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
fi

if command -v npm &>/dev/null; then
  if [[ -f package-lock.json ]]; then
    npm ci --omit=dev
  else
    npm install --omit=dev
  fi
  npm run build
else
  echo "UYARI: npm bulunamadı; Vite build atlandı. Node kurulu olduğundan emin olun."
fi

php artisan migrate --force

php artisan storage:link 2>/dev/null || php artisan storage:link --force 2>/dev/null || true

composer run forge-optimize --no-interaction

php artisan event:cache 2>/dev/null || true

php artisan queue:restart 2>/dev/null || true

php artisan up 2>/dev/null || true

echo "==> Deploy tamamlandı."
