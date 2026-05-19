# simdibildir.com — Laravel Forge dağıtım rehberi

Bu belge, projeyi Forge üzerinde **çalışır durumda** yayına almak için adım adım kontrol listesidir.

## 1. Forge site ayarları

| Ayar | Değer |
|------|--------|
| Domain | `simdibildir.com` (+ `www` yönlendirmesi isteğe bağlı) |
| Web directory | `public` |
| PHP | 8.3 veya üzeri |
| Repository | `erkanulker23/bildirfix` (veya taşıdığınız repo) |
| Branch | `main` |
| Node | LTS (Vite build için; Forge “Install Node” veya nvm) |

**SSL:** Let’s Encrypt veya Cloudflare Full (strict). Cloudflare kullanıyorsanız `.env` içinde `FORCE_HTTPS=true` yapın.

## 2. Veritabanı ve Redis

1. Forge’da **MySQL** veritabanı oluşturun.
2. **Redis** sunucusunu etkinleştirin (önerilir: cache + session + queue).
3. Site → **Environment** bölümünde `.env.example` dosyasını referans alarak doldurun.

### Üretim `.env` özeti

```env
APP_NAME=simdibildir.com
APP_ENV=production
APP_DEBUG=false
APP_URL=https://simdibildir.com
FORCE_HTTPS=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=forge
DB_PASSWORD=...

CACHE_STORE=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

BROADCAST_CONNECTION=null

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=destek@simdibildir.com
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=simdibildir.com,www.simdibildir.com
```

`APP_KEY` boşsa sunucuda bir kez: `php artisan key:generate --show` çıktısını Environment’a yapıştırın.

İsteğe bağlı: Cloudflare Turnstile, Google OAuth, Google Maps (`CLOUDFLARE_*`, `GOOGLE_*` — `.env.example` içinde).

## 3. Deploy Script (Forge paneli)

Forge → Site → **Deployment** → Deploy Script alanına aşağıyı yapıştırın veya repodaki betiği çağırın:

```bash
cd $FORGE_SITE_PATH
git pull origin $FORGE_SITE_BRANCH
bash scripts/forge-deploy.sh
```

`scripts/forge-deploy.sh` şunları yapar: Composer (prod), `npm ci` + `npm run build`, migrate, `storage:link`, config/route/view cache, optimize, queue restart.

## 4. İlk kurulum (bir kez)

Deploy’dan sonra SSH ile sunucuya bağlanın:

```bash
cd /home/forge/simdibildir.com   # kendi site yolunuz
bash scripts/forge-bootstrap.sh
```

Bu betik:

- Temel **kategorileri** yükler (`EssentialDataSeeder`)
- **81 il / ilçe** verisini çeker (`turkiye:sync-geo`)
- **Kurumları** yükler (`institutions:seed-turkey`; logolar repoda olduğu için `--no-logos`)

> **Uyarı:** `php artisan db:seed` (tam demo) yalnızca geliştirmede kullanın; üretimde çalıştırmayın.

## 5. Scheduler ve queue (Forge Daemons)

**Scheduler** (Forge → Scheduler):

```
* * * * * cd /home/forge/SITE_PATH && php artisan schedule:run >> /dev/null 2>&1
```

**Queue worker** (`QUEUE_CONNECTION=redis` ise, Forge → Queue / Daemon):

```
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
```

Şu an uygulama ağırlıklı olarak senkron çalışır; queue ileride bildirim/e-posta için hazırdır.

## 6. Sağlık kontrolü

- `https://simdibildir.com/up` → Laravel health (200)
- Ana sayfa, `/nasil-calisir`, `/paylasim-olustur` açılıyor mu
- `public/build` deploy sırasında üretilir (repoda commit edilmez)

## 7. Sorun giderme

| Belirti | Çözüm |
|---------|--------|
| CSS/JS yok | Sunucuda Node kurulu mu; deploy log’da `npm run build` başarılı mı |
| 500 / APP_KEY | Environment’da `APP_KEY` tanımlı mı |
| İl/ilçe listesi boş | `php artisan turkiye:sync-geo` |
| Kurum araması boş | `php artisan institutions:seed-turkey --no-logos` |
| Seeder “Command cancelled” (production) | `--force` ekleyin: `php artisan db:seed --class=CampaignTopicSeeder --force` veya `bash scripts/forge-seed-reference-data.sh` |
| Kampanya konuları / reklam alanları boş | Deploy sonrası otomatik seed edilir; manuel: `bash scripts/forge-seed-reference-data.sh` |
| Blog yazıları yok | `php artisan db:seed --class=ProjectBlogSeeder --force` (18 makale; güvenli tekrar çalıştırılabilir) |
| Karışık içerik (http) | `APP_URL=https://...`, `FORCE_HTTPS=true` |
| Oturum sorunu | `SESSION_DRIVER=database`, `sessions` tablosu migrate edildi mi |

## 8. Güncellemeler

Her `git push` sonrası Forge otomatik deploy edebilir. Manuel: Forge → **Deploy Now**.
