# simdibildir.com

Kent şikâyetleri, moderasyon ve kampanyalar için Laravel tabanlı uygulama. Canlı site: [simdibildir.com](https://simdibildir.com).

## Yerel kurulum

```bash
composer setup
# veya: composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && npm ci && npm run build
php artisan db:seed   # örnek veri (üretimde çalıştırmayın)
php artisan serve
```

Yerel geliştirmede `.env` içinde `APP_URL=http://simdibildir.test` ve Sanctum domain’lerini kendi local alan adınıza göre ayarlayın (Laravel Herd/Valet).

## Üretim — Laravel Forge

Ayrıntılı kontrol listesi: **[DEPLOY.md](DEPLOY.md)**

1. Forge’da site oluşturun (`simdibildir.com`, web root: `public`, PHP 8.3+).
2. Environment’ı `.env.example` üretim notlarına göre doldurun.
3. **Deploy Script:**

```bash
cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH
bash scripts/forge-deploy.sh
```

4. İlk kurulumda SSH ile **bir kez:** `bash scripts/forge-bootstrap.sh`
5. Scheduler: `* * * * * cd $FORGE_SITE_PATH && php artisan schedule:run`
6. Queue (redis): `php artisan queue:work redis --sleep=3 --tries=3`

Sağlık kontrolü: `GET /up`

## Lisans

MIT
