# BildirFIX

Kent şikâyetleri, moderasyon ve kampanyalar için Laravel tabanlı uygulama. Kaynak kod: [github.com/erkanulker23/bildirfix](https://github.com/erkanulker23/bildirfix).

## Yerel kurulum

```bash
composer setup
# veya: composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && npm ci && npm run build
php artisan db:seed   # örnek veri (üretimde çalıştırmayın)
php artisan serve
```

Geliştirmede kuyruk/Reverb için bakınız: `.env.example` içindeki `QUEUE_CONNECTION`, `REVERB_*` değişkenleri ve `composer dev` script’i.

## Üretim — Laravel Forge

1. **Forge**’da yeni site oluşturun, depoyu bağlayın (`erkanulker23/bildirfix`) ve PHP 8.3+ seçin.
2. **Web dizini** Laravel için `public` olarak kalsın.
3. **Çevre**: `.env.example` dosyasını kopyalayıp Forge “Environment” üzerinden doldurun. Mutlaka ayarlayın: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://alanadiniz...`, `FORCE_HTTPS=true` (CDN/proxy arkasındaysanız), güçlü `APP_KEY`, veritabanı, `QUEUE_CONNECTION` (genelde `redis` veya iş yoksa `sync`), `CACHE_STORE` (`redis` önerilir).
4. **Deploy script** örneği (Forge “Deploy Script” alanına uygun; proje kökü genelde `releases/...` içinde olur — Forge değişkeni `$FORGE_SITE_PATH` kullanın):

```bash
cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-dev --prefer-dist --no-interaction --optimize-autoloader

npm ci --omit=dev
npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan optimize

php artisan queue:restart
```

Tek komut olarak da `./scripts/forge-deploy.sh` kullanılabilir (içeriği Forge betiğiyle uyumludur; sunucuda yürütülmeden önce `chmod +x scripts/forge-deploy.sh` gerekebilir).

5. **Zamanlanmış işler**: Forge Scheduler’da `$FORGE_SITE_PATH` için `php artisan schedule:run` her dakika.
6. **Kuyruk**: Redis kullanıyorsanız Forge “Daemons” ile `php artisan queue:work redis --sleep=3 --tries=3`.
7. **Reverb/WebSocket**: Canlı bildirim kullanılacaksa Forge’da ayrı daemon ve uygun TLS/proxy gerekebilir; kullanılmıyorsa `.env`’de devre dışı bırakılabilir veya istemci bu özelliği kullanmaz.

`bootstrap/app.php` içinde proxilere güven ayarı (Cloudflare vb.) yapılmıştır.

## Güvenlik ve örnek veri

Üretimde **`php artisan db:seed` kullanmayın**; örnek hesapların şifreleri yalnızca geliştirme içindir. Süper admin ve diğer demo kullanıcılar `database/seeders/DatabaseSeeder.php` içindedir — canlı ortamdan önce kaldırın veya sıfırlayın.

## Test

```bash
composer test
```

## Lisans

Laravel bileşenleri MIT ile dağıtılır.
