#!/usr/bin/env bash
# Laravel Forge — site ilk kurulumunda BİR KEZ çalıştırın (migrate sonrası).
# SSH: cd $FORGE_SITE_PATH && bash scripts/forge-bootstrap.sh
set -euo pipefail

ROOT="${FORGE_SITE_PATH:-$(cd "$(dirname "$0")/.." && pwd)}"
cd "$ROOT"

echo "==> İlk kurulum: $ROOT"

php artisan migrate --force

php artisan db:seed --class=EssentialDataSeeder --force

echo "==> Türkiye il/ilçe verisi (TurkiyeAPI)…"
php artisan turkiye:sync-geo

echo "==> Kurumlar (logolar repoda varsa --no-logos önerilir)…"
php artisan institutions:seed-turkey --no-logos --sync-geo

echo "==> İlk kurulum tamamlandı."
echo "    Üretimde DatabaseSeeder (demo veri) ÇALIŞTIRMAYIN."
