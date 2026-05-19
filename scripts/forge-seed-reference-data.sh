#!/usr/bin/env bash
# Üretimde kampanya konuları ve reklam yerleşimleri (updateOrCreate — güvenli).
# Forge → Commands veya SSH:
#   cd $FORGE_SITE_PATH && bash scripts/forge-seed-reference-data.sh
set -euo pipefail

ROOT="${FORGE_SITE_PATH:-$(cd "$(dirname "$0")/.." && pwd)}"
cd "$ROOT"

echo "==> Referans verileri (production)…"

php artisan db:seed --class=CampaignTopicSeeder --force --no-interaction
php artisan db:seed --class=AdPlacementSeeder --force --no-interaction

# Proje blog yazıları (SMA, kampanya, kent bildirimi — updateOrCreate ile güvenli)
php artisan db:seed --class=ProjectBlogSeeder --force --no-interaction

echo "==> Tamamlandı."
