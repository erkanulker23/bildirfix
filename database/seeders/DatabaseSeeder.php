<?php

namespace Database\Seeders;

use App\Enums\CampaignModerationStatus;
use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Campaign;
use App\Models\CampaignComment;
use App\Models\CampaignSupporter;
use App\Models\Category;
use App\Models\City;
use App\Models\Comment;
use App\Models\District;
use App\Models\Institution;
use App\Models\Neighborhood;
use App\Models\Post;
use App\Models\Story;
use App\Models\Support;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Altyapı', 'slug' => 'altyapi'],
            ['name' => 'Ulaşım', 'slug' => 'ulasim'],
            ['name' => 'Çevre ve Temizlik', 'slug' => 'cevre-ve-temizlik'],
            ['name' => 'Gürültü', 'slug' => 'gurultu'],
            ['name' => 'Güvenlik', 'slug' => 'guvenlik'],
        ];

        foreach ($categories as $order => $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'sort_order' => $order,
                ],
            );
        }

        $istanbul = City::query()->updateOrCreate(
            ['plate' => 34],
            [
                'name' => 'İstanbul',
                'slug' => 'istanbul',
                'latitude' => 41.0082,
                'longitude' => 28.9784,
            ],
        );

        $atasehir = District::query()->updateOrCreate(
            ['city_id' => $istanbul->id, 'slug' => 'atasehir'],
            ['name' => 'Ataşehir'],
        );

        $nh = [];
        foreach (['İçerenköy' => 'icerenkoy', 'Barbaros' => 'barbaros', 'Küçükbakkalköy' => 'kucukbakkalkoy'] as $name => $slug) {
            $nh[$slug] = Neighborhood::query()->updateOrCreate(
                ['district_id' => $atasehir->id, 'slug' => $slug],
                ['name' => $name],
            );
        }

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'erkanulker0@gmail.com'],
            [
                'name' => 'Süper Yönetici',
                'phone' => Phone::normalize('5530000099'),
                'password' => Hash::make('Yagmur160315'),
                'role' => UserRole::SuperAdmin,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        $admin = User::query()->updateOrCreate(
            ['phone' => Phone::normalize('5530000001')],
            [
                'name' => 'Bildir Admin',
                'email' => 'admin@sorunbildir.local',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        $institutionUser = User::query()->updateOrCreate(
            ['phone' => Phone::normalize('5530000002')],
            [
                'name' => 'Demo Kurum Hesabı',
                'email' => 'iski@sorunbildir.local',
                'password' => Hash::make('password'),
                'role' => UserRole::Institution,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
                'email_verified_at' => now(),
            ],
        );

        $iski = Institution::query()->updateOrCreate(
            ['name' => 'İSKİ'],
            [
                'city_id' => $istanbul->id,
                'account_user_id' => $institutionUser->id,
                'type' => 'utilities',
                'verified' => true,
            ],
        );

        $u1 = User::query()->updateOrCreate(
            ['phone' => Phone::normalize('5530000003')],
            [
                'name' => 'Ayşe Yılmaz',
                'email' => 'ayse@ornek.local',
                'password' => Hash::make('password'),
                'role' => UserRole::VerifiedUser,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
            ],
        );

        $u2 = User::query()->updateOrCreate(
            ['phone' => Phone::normalize('5530000004')],
            [
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet@ornek.local',
                'password' => Hash::make('password'),
                'role' => UserRole::VerifiedUser,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
            ],
        );

        $u3 = User::query()->updateOrCreate(
            ['phone' => Phone::normalize('5530000005')],
            [
                'name' => 'Zeynep Arslan',
                'email' => 'zeynep@ornek.local',
                'password' => Hash::make('password'),
                'role' => UserRole::VerifiedUser,
                'verification_status' => VerificationStatus::Verified,
                'phone_verified_at' => now(),
            ],
        );

        $catUl = Category::query()->where('slug', 'ulasim')->firstOrFail();
        $catCe = Category::query()->where('slug', 'cevre-ve-temizlik')->firstOrFail();
        $catAlt = Category::query()->where('slug', 'altyapi')->firstOrFail();
        $catGur = Category::query()->where('slug', 'gurultu')->firstOrFail();
        $catGun = Category::query()->where('slug', 'guvenlik')->firstOrFail();

        $bedas = Institution::query()->updateOrCreate(
            ['name' => 'BEDAŞ'],
            [
                'city_id' => $istanbul->id,
                'account_user_id' => null,
                'type' => 'utilities',
                'verified' => true,
            ],
        );

        $p1 = Post::query()->updateOrCreate(
            ['title' => 'Ataşehir İçerenköy’de kopmuş kaldırım plakası'],
            [
                'user_id' => $u1->id,
                'description' => 'Okul çıkış saatleri çok yoğun, kaldırımda yaklaşık 3 metrelik bölüm kırık. Çocuklar yola savruluyor. Acil bakım bekliyor.',
                'media_url' => 'https://images.unsplash.com/photo-1573495804683-e11c6f9b2c7c?auto=format&fit=crop&w=900&q=80',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1573495804683-e11c6f9b2c7c?auto=format&fit=crop&w=900&q=80']],
                'type' => 'complaint',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'neighborhood_id' => $nh['icerenkoy']->id,
                'latitude' => 40.9772,
                'longitude' => 29.1044,
                'category_id' => $catAlt->id,
                'institution_id' => $iski->id,
                'status' => PostStatus::InProgress,
                'moderation_status' => PostModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
            ],
        );

        $p2 = Post::query()->updateOrCreate(
            ['title' => 'Barbaros Mahallesi’nde sokak lambası 2 haftadır yanmıyor'],
            [
                'user_id' => $u2->id,
                'description' => 'Karanlık sokak güvenlik sorunu yaratıyor. BEDAŞ’a iletildi ama dönüş yok; buradan da duyurmak istedim.',
                'media_url' => 'https://www.youtube.com/watch?v=JfVOs4VYSk0',
                'media' => [['type' => 'video', 'url' => 'https://www.youtube.com/watch?v=JfVOs4VYSk0']],
                'type' => 'complaint',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'neighborhood_id' => $nh['barbaros']->id,
                'latitude' => 40.9831,
                'longitude' => 29.1242,
                'category_id' => $catAlt->id,
                'institution_id' => $bedas->id,
                'status' => PostStatus::Open,
                'moderation_status' => PostModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
            ],
        );

        $p3 = Post::query()->updateOrCreate(
            ['title' => 'Küçükbakkalköy’de yanlış park edilen araçlar otobüs hattını kapatıyor'],
            [
                'user_id' => $u3->id,
                'description' => 'Sabah 08:00–09:30 arası dükkan önünde çift sıra duran araçlar otobüsün dönüşünü engelliyor. Zaptiye ve UKOME bilgisiyle paylaşıyorum.',
                'media_url' => 'https://images.unsplash.com/photo-1570125909232-eccb93ef7666?auto=format&fit=crop&w=880&q=80',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1570125909232-eccb93ef7666?auto=format&fit=crop&w=880&q=80']],
                'type' => 'complaint',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'neighborhood_id' => $nh['kucukbakkalkoy']->id,
                'category_id' => $catUl->id,
                'institution_id' => null,
                'status' => PostStatus::Open,
                'moderation_status' => PostModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
            ],
        );

        $p4 = Post::query()->updateOrCreate(
            ['title' => 'Çöp kutularının haftalık olarak boşaltılmadığı park alanı'],
            [
                'user_id' => $u1->id,
                'description' => 'Parkta 4 adet dolu çöp kutusu var, çevreye yayılıyor. Temizlik ekiplerinden haftalık plan rica ediyorum.',
                'media_url' => null,
                'type' => 'complaint',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'category_id' => $catCe->id,
                'institution_id' => null,
                'status' => PostStatus::Resolved,
                'moderation_status' => PostModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
            ],
        );

        $p5 = Post::query()->updateOrCreate(
            ['title' => 'Gece 01:00 sonrası inşaat gürültüsü'],
            [
                'user_id' => $u2->id,
                'description' => 'Apartman yan tarafında gece sessizlik saatinde hilti kullanılıyor. Yasal süreleri aştığını düşünüyorum.',
                'media_url' => null,
                'type' => 'complaint',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'category_id' => $catGur->id,
                'institution_id' => null,
                'status' => PostStatus::Rejected,
                'moderation_status' => PostModerationStatus::Rejected,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => 'Şikâyet yayına uygun görülmedi.',
            ],
        );

        foreach ([$p1, $p2, $p3, $p4, $p5] as $post) {
            Comment::query()->firstOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $admin->id,
                ],
                [
                    'content' => 'Kayıt ilgili ekiplere iletildi. Lütfen fotoğraf ve kesin adres bilgisini mesajdan paylaşın.',
                ],
            );
        }

        Comment::query()->firstOrCreate(
            [
                'post_id' => $p1->id,
                'user_id' => $u2->id,
            ],
            [
                'content' => 'Ben de aynı güzergâhı kullanıyorum, gerçekten tehlikeli. Destek veriyorum.',
            ],
        );

        foreach ([$u1, $u2, $u3, $admin] as $u) {
            foreach ([$p1, $p2, $p3] as $post) {
                Support::query()->firstOrCreate(
                    [
                        'user_id' => $u->id,
                        'post_id' => $post->id,
                    ],
                );
            }
        }

        Story::query()->updateOrCreate(
            [
                'user_id' => $u1->id,
                'description' => 'İçerenköy’de yağmur sonrası biriken su — dikkat!',
            ],
            [
                'media_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=400&q=82',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'latitude' => 40.9772,
                'longitude' => 29.1044,
                'expires_at' => now()->addHours(20),
            ],
        );

        Story::query()->updateOrCreate(
            [
                'user_id' => $u3->id,
                'description' => 'Barbaros’ta yeni banklar çok iyi olmuş.',
            ],
            [
                'media_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=400&q=82',
                'city_id' => $istanbul->id,
                'district_id' => $atasehir->id,
                'expires_at' => now()->addHours(18),
            ],
        );

        $ankara = City::query()->updateOrCreate(
            ['plate' => 6],
            [
                'name' => 'Ankara',
                'slug' => 'ankara',
                'latitude' => 39.9334,
                'longitude' => 32.8597,
            ],
        );

        $cankaya = District::query()->updateOrCreate(
            ['city_id' => $ankara->id, 'slug' => 'cankaya'],
            ['name' => 'Çankaya'],
        );

        foreach (
            [
                ['5530000120', 'Kerem Acar', 'kerem@demo.local'],
                ['5530000121', 'Selin Erdem', 'selin@demo.local'],
                ['5530000122', 'Ömer Türk', 'omer@demo.local'],
                ['5530000123', 'Damla Güneş', 'damla@demo.local'],
                ['5530000124', 'Cem Bozkurt', 'cem@demo.local'],
            ] as [$p, $nm, $em]
        ) {
            User::query()->updateOrCreate(
                ['phone' => Phone::normalize('+90'.$p)],
                [
                    'name' => $nm,
                    'email' => $em,
                    'password' => Hash::make('password'),
                    'role' => UserRole::VerifiedUser,
                    'verification_status' => VerificationStatus::Verified,
                    'phone_verified_at' => now(),
                ],
            );
        }

        $kerem = User::query()->where('phone', Phone::normalize('+905530000120'))->firstOrFail();
        $selin = User::query()->where('phone', Phone::normalize('+905530000121'))->firstOrFail();

        foreach (
            [
                [
                    'title' => 'Kızılay’da yayalar için tehlikeli mazgal aralığı',
                    'slug_key' => 'ankara-mazgal-kizilay',
                    'user' => $kerem,
                    'desc' => 'Yağmur sonrası açılmış olan çukur yakınında mazgal ara paçasında çocuk oyun alanı ile kesişiyor.',
                    'cat' => $catAlt,
                    'district' => $cankaya,
                    'nh' => null,
                    'lat' => 39.92077,
                    'lng' => 32.85411,
                    'inst' => null,
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Bahçelievler Mahallesi yan yolda kopmuş süpürgelik',
                    'slug_key' => 'ankara-supergelik-yan-yol',
                    'user' => $selin,
                    'desc' => 'Sabah işe gidiş sırasında kaldırım daralmış görünüm var; araç sıyırması risk oluşturuyor.',
                    'cat' => $catUl,
                    'district' => $cankaya,
                    'nh' => null,
                    'lat' => 39.9125,
                    'lng' => 32.8388,
                    'inst' => $bedas,
                    'status' => PostStatus::InProgress,
                ],
                [
                    'title' => 'İçerenköy’de sokak köpekleri için su kabı talebi',
                    'slug_key' => 'istanbul-sokak-hayvanlari-su',
                    'user' => $u3,
                    'desc' => 'Park yakınında öğle sıcağında gönüllüler su taşımak zorunda kalıyor; sabit içme noktası rica ediyoruz.',
                    'cat' => $catCe,
                    'district' => $atasehir,
                    'nh' => $nh['barbaros'] ?? null,
                    'lat' => 40.985,
                    'lng' => 29.118,
                    'inst' => null,
                    'status' => PostStatus::Open,
                ],
            ] as $rowPost
        ) {
            Post::query()->updateOrCreate(
                ['title' => $rowPost['title']],
                [
                    'user_id' => $rowPost['user']->id,
                    'description' => $rowPost['desc'],
                    'media_url' => 'https://images.unsplash.com/photo-1574259392081-aebb40fecda3?auto=format&fit=crop&w=880&q=80',
                    'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1574259392081-aebb40fecda3?auto=format&fit=crop&w=880&q=80']],
                    'type' => 'complaint',
                    'city_id' => $rowPost['user'] === $kerem || $rowPost['user'] === $selin ? $ankara->id : $istanbul->id,
                    'district_id' => $rowPost['district']->id,
                    'neighborhood_id' => $rowPost['nh']?->id,
                    'latitude' => $rowPost['lat'],
                    'longitude' => $rowPost['lng'],
                    'category_id' => $rowPost['cat']->id,
                    'institution_id' => $rowPost['inst']?->id,
                    'status' => $rowPost['status'],
                    'moderation_status' => PostModerationStatus::Approved,
                    'moderated_at' => now(),
                    'moderated_by_user_id' => $superAdmin->id,
                ],
            );
        }

        Story::query()->updateOrCreate(
            [
                'user_id' => $kerem->id,
                'description' => 'Bahçeli’te yeni yapılmış bisiklet hattına teşekkürler!',
            ],
            [
                'media_url' => 'https://images.unsplash.com/photo-1474552228711-9453fb41302d?auto=format&fit=crop&w=400&q=82',
                'city_id' => $ankara->id,
                'district_id' => $cankaya->id,
                'expires_at' => now()->addHours(36),
            ],
        );

        $kadikoyDistrict = District::query()->updateOrCreate(
            ['city_id' => $istanbul->id, 'slug' => 'kadikoy'],
            ['name' => 'Kadıköy'],
        );
        Neighborhood::query()->updateOrCreate(
            ['district_id' => $kadikoyDistrict->id, 'slug' => 'moda'],
            ['name' => 'Moda'],
        );
        Neighborhood::query()->updateOrCreate(
            ['district_id' => $kadikoyDistrict->id, 'slug' => 'feneryolu'],
            ['name' => 'Feneryolu'],
        );

        $besiktasDistrict = District::query()->updateOrCreate(
            ['city_id' => $istanbul->id, 'slug' => 'besiktas'],
            ['name' => 'Beşiktaş'],
        );

        $umraniyeDistrict = District::query()->updateOrCreate(
            ['city_id' => $istanbul->id, 'slug' => 'umraniye'],
            ['name' => 'Ümraniye'],
        );

        $izmir = City::query()->updateOrCreate(
            ['plate' => 35],
            [
                'name' => 'İzmir',
                'slug' => 'izmir',
                'latitude' => 38.4237,
                'longitude' => 27.1428,
            ],
        );

        $konak = District::query()->updateOrCreate(
            ['city_id' => $izmir->id, 'slug' => 'konak'],
            ['name' => 'Konak'],
        );

        $bornovaDistrict = District::query()->updateOrCreate(
            ['city_id' => $izmir->id, 'slug' => 'bornova'],
            ['name' => 'Bornova'],
        );

        Neighborhood::query()->updateOrCreate(
            ['district_id' => $konak->id, 'slug' => 'alsancak'],
            ['name' => 'Alsancak'],
        );

        Neighborhood::query()->updateOrCreate(
            ['district_id' => $konak->id, 'slug' => 'cumhuriyet-meydani'],
            ['name' => 'Cumhuriyet Meydanı'],
        );

        $bursa = City::query()->updateOrCreate(
            ['plate' => 16],
            [
                'name' => 'Bursa',
                'slug' => 'bursa',
                'latitude' => 40.1826,
                'longitude' => 29.0665,
            ],
        );
        $nilufer = District::query()->updateOrCreate(
            ['city_id' => $bursa->id, 'slug' => 'nilufer'],
            ['name' => 'Nilüfer'],
        );
        $osmangazi = District::query()->updateOrCreate(
            ['city_id' => $bursa->id, 'slug' => 'osmangazi'],
            ['name' => 'Osmangazi'],
        );
        Neighborhood::query()->updateOrCreate(
            ['district_id' => $nilufer->id, 'slug' => 'görükle'],
            ['name' => 'Görükle'],
        );

        $antalya = City::query()->updateOrCreate(
            ['plate' => 7],
            [
                'name' => 'Antalya',
                'slug' => 'antalya',
                'latitude' => 36.8969,
                'longitude' => 30.7133,
            ],
        );
        $muratpasa = District::query()->updateOrCreate(
            ['city_id' => $antalya->id, 'slug' => 'muratpasa'],
            ['name' => 'Muratpaşa'],
        );
        $kepez = District::query()->updateOrCreate(
            ['city_id' => $antalya->id, 'slug' => 'kepez'],
            ['name' => 'Kepez'],
        );

        $adana = City::query()->updateOrCreate(
            ['plate' => 1],
            [
                'name' => 'Adana',
                'slug' => 'adana',
                'latitude' => 37.0000,
                'longitude' => 35.3213,
            ],
        );
        $seyhan = District::query()->updateOrCreate(
            ['city_id' => $adana->id, 'slug' => 'seyhan'],
            ['name' => 'Seyhan'],
        );
        $cukurovaIlce = District::query()->updateOrCreate(
            ['city_id' => $adana->id, 'slug' => 'cukurova-adana'],
            ['name' => 'Çukurova'],
        );

        $konya = City::query()->updateOrCreate(
            ['plate' => 42],
            [
                'name' => 'Konya',
                'slug' => 'konya',
                'latitude' => 37.8746,
                'longitude' => 32.4932,
            ],
        );
        $selcuklu = District::query()->updateOrCreate(
            ['city_id' => $konya->id, 'slug' => 'selcuklu'],
            ['name' => 'Selçuklu'],
        );
        $meram = District::query()->updateOrCreate(
            ['city_id' => $konya->id, 'slug' => 'meram'],
            ['name' => 'Meram'],
        );

        $gaziantep = City::query()->updateOrCreate(
            ['plate' => 27],
            [
                'name' => 'Gaziantep',
                'slug' => 'gaziantep',
                'latitude' => 37.0662,
                'longitude' => 37.3833,
            ],
        );
        $sahinbey = District::query()->updateOrCreate(
            ['city_id' => $gaziantep->id, 'slug' => 'sahinbey'],
            ['name' => 'Şahinbey'],
        );
        $sehitkamil = District::query()->updateOrCreate(
            ['city_id' => $gaziantep->id, 'slug' => 'sehitkamil'],
            ['name' => 'Şehitkamil'],
        );

        $trabzon = City::query()->updateOrCreate(
            ['plate' => 61],
            [
                'name' => 'Trabzon',
                'slug' => 'trabzon',
                'latitude' => 41.0027,
                'longitude' => 39.7168,
            ],
        );
        $ortahisar = District::query()->updateOrCreate(
            ['city_id' => $trabzon->id, 'slug' => 'ortahisar'],
            ['name' => 'Ortahisar'],
        );

        $eskisehir = City::query()->updateOrCreate(
            ['plate' => 26],
            [
                'name' => 'Eskişehir',
                'slug' => 'eskisehir',
                'latitude' => 39.7767,
                'longitude' => 30.5206,
            ],
        );
        $odunpazari = District::query()->updateOrCreate(
            ['city_id' => $eskisehir->id, 'slug' => 'odunpazari'],
            ['name' => 'Odunpazarı'],
        );
        $tepebasi = District::query()->updateOrCreate(
            ['city_id' => $eskisehir->id, 'slug' => 'tepebasi'],
            ['name' => 'Tepebaşı'],
        );

        $mersin = City::query()->updateOrCreate(
            ['plate' => 33],
            [
                'name' => 'Mersin',
                'slug' => 'mersin',
                'latitude' => 36.8121,
                'longitude' => 34.6415,
            ],
        );
        $yeniehirMersin = District::query()->updateOrCreate(
            ['city_id' => $mersin->id, 'slug' => 'yenişehir-mersin'],
            ['name' => 'Yenişehir'],
        );

        $omer = User::query()->where('phone', Phone::normalize('+905530000122'))->firstOrFail();
        $damla = User::query()->where('phone', Phone::normalize('+905530000123'))->firstOrFail();
        $cem = User::query()->where('phone', Phone::normalize('+905530000124'))->firstOrFail();

        $demoImg = fn (string $id) => 'https://images.unsplash.com/photo-'.$id.'?auto=format&fit=crop&w=880&q=78';

        foreach (
            [
                [
                    'title' => 'Kadıköy Moda kıyısında sığ bottan düşen plastik atıklar',
                    'user' => $damla,
                    'desc' => 'Rüzgârla sığ alana biriken parçalar yürüyüş yoluna taşıyor; özellikle hafta sonu yoğunluğunda tehlike oluşturuyor.',
                    'cat' => $catCe,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9787,
                    'lng' => 29.0264,
                    'img' => $demoImg('1618477388954-7852f3267273'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Feneryolu Mahallesi yan sokakta kırık kamera kutusu kapısı',
                    'user' => $cem,
                    'desc' => 'Şantiye yakını güzergâhta metal kapak çıkmış; özellikle gece sürtünme ve çocuk oyun güvenliği için risk oluşturuyor.',
                    'cat' => $catGun,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9902,
                    'lng' => 29.0365,
                    'img' => $demoImg('1517976480424-48aac5c6c6cf'),
                    'status' => PostStatus::InProgress,
                ],
                [
                    'title' => 'Alsancak’ta elektrik kutusu koruyucusu yerinden oynayıp kaldırımı daraltmış',
                    'user' => $omer,
                    'desc' => 'Gece güzergâhından geçişte sırt sırta geçilir hale geldi; bağlantı vidası çıkmış gibi görünüyor.',
                    'cat' => $catAlt,
                    'district' => $konak,
                    'lat' => 38.4381,
                    'lng' => 27.1432,
                    'img' => $demoImg('1558618666-fcd25c85cdaa'),
                    'inst' => $bedas,
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Bornova kampüs yakını elektrik dolabında eksik uyarı işaretleri',
                    'user' => $kerem,
                    'desc' => 'Öğrenci çıkış saatleri yoğunken uyarı levhası görünmesi gerekiyordu.',
                    'cat' => $catGun,
                    'district' => $bornovaDistrict,
                    'lat' => 38.4596,
                    'lng' => 27.2264,
                    'img' => $demoImg('1581092160569-8929eaf89d5d'),
                    'inst' => $bedas,
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Konak ring bağlantısında engelli çıkışı için dar rampa bildirimi',
                    'user' => $selin,
                    'desc' => 'Eğim yaklaşık yüzde 12 hissiyatı oluşturuyor; tekerlekli sandalye için standart altı olabileceği izlenimi.',
                    'cat' => $catUl,
                    'district' => $konak,
                    'lat' => 38.4192,
                    'lng' => 27.1287,
                    'img' => $demoImg('1586528116792-4848d5c5d865'),
                    'status' => PostStatus::InProgress,
                ],
                [
                    'title' => 'Kordon boyu kafe komşusu yoğun müzik yayını (cumartesi gece davranış bildirimi)',
                    'user' => $u2,
                    'desc' => 'Apartman bloklarına yakın mesafeden hoparlörle yüksek seviye yayın; nöbet bildirilmişti.',
                    'cat' => $catGur,
                    'district' => $konak,
                    'lat' => 38.4405,
                    'lng' => 27.1463,
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Bahçelievler minibüs durağı çökük su birikintisi zemini',
                    'user' => $damla,
                    'desc' => 'Yağmursuz gün bile çamur oluşmakta.',
                    'cat' => $catAlt,
                    'district' => $cankaya,
                    'lat' => 39.905,
                    'lng' => 32.842,
                    'img' => $demoImg('1503676260728-bb5d7e9d5c5c'),
                    'status' => PostStatus::Resolved,
                ],
                [
                    'title' => 'Şehir hastanesi yakını ambulans sırasına blokaj bildirilmiş ticari minibüs',
                    'user' => $cem,
                    'desc' => 'Sabah nöbet başlangıcında sıra kayması yaşandığı aktarılmıştır.',
                    'cat' => $catUl,
                    'district' => $cankaya,
                    'lat' => 39.9178,
                    'lng' => 32.8585,
                    'img' => $demoImg('1449824913935-d7d6c9816fea'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Barbaros Mahallesi sızdıran geri dönüşüm kutuları ve koku riski',
                    'user' => $selin,
                    'desc' => 'Fare ve koku endişesi; koordineli temizlik rica.',
                    'cat' => $catCe,
                    'district' => $atasehir,
                    'nh' => $nh['barbaros'] ?? null,
                    'lat' => 40.984,
                    'lng' => 29.12,
                    'img' => $demoImg('1532996120744-ab7c4c79d93c'),
                    'status' => PostStatus::InProgress,
                ],
                [
                    'title' => 'Metro üst geçit merdiven basamakları kaygan yüzey (bakım bildirimi)',
                    'user' => $u3,
                    'desc' => 'Yağmur sonrası kayma artıyormuş gibi bildirildi.',
                    'cat' => $catAlt,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9936,
                    'lng' => 29.0267,
                    'img' => $demoImg('1519336335483-9488c92c5d81'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Kent ormanı giriş etabında araç sıyrığı ve doğa güvenlik kaygısı',
                    'user' => $kerem,
                    'desc' => 'Yangın güvenliği için erişimin denetlenebilmesi adına kayıtlı bildirimi paylaşıyorum.',
                    'cat' => $catCe,
                    'district' => $cankaya,
                    'lat' => 39.894,
                    'lng' => 32.867,
                    'img' => $demoImg('1500530855697-b586d89ba3ee'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Servis yoğun sokağında sabahcı korna zinciri — bilgilendirme talebi',
                    'user' => $omer,
                    'desc' => 'Okula yakın güzergâhta sık aralıklı kullanımdan yakınmahal bildirimi.',
                    'cat' => $catGur,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9868,
                    'lng' => 29.0362,
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Güvenlik kiosları haritasının görünürlüğü için platform geri bildirimi',
                    'user' => $damla,
                    'desc' => 'Kent haritasına manuel bildirilmiş sabit kiosk noktaları daha belirgin olsun isteği.',
                    'cat' => $catGun,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9825,
                    'lng' => 29.0291,
                    'img' => $demoImg('1574259392081-aebb40fecda3'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Körfez çevresi lağım kokusu sırasında kaçınım bandı talebi',
                    'user' => $omer,
                    'desc' => 'Geçiş yoğunken geçici uyarıların planlanması rica olunur.',
                    'cat' => $catCe,
                    'district' => $konak,
                    'lat' => 38.4288,
                    'lng' => 27.1466,
                    'status' => PostStatus::InProgress,
                ],
                [
                    'title' => 'E‑ticaret kurye yoğunluğu ile kapanan yan sokak yaya güvenliği',
                    'user' => $selin,
                    'desc' => 'İki yönde sıkışma ve yayanın maruz kalması kaydı.',
                    'cat' => $catUl,
                    'district' => $atasehir,
                    'nh' => $nh['kucukbakkalkoy'] ?? null,
                    'lat' => 40.9855,
                    'lng' => 29.1228,
                    'img' => $demoImg('1528909516456-8399fdb7f5d9'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Dev parkında budama takvimi hakkında süreç bildirimi talebi',
                    'user' => $u1,
                    'desc' => 'Kuş yuvaları dönemiyle çakışmaması için yıllık bakım planı yayınlasın isteği.',
                    'cat' => $catCe,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9863,
                    'lng' => 29.0368,
                    'img' => $demoImg('1501594900653-91d4c012a5c4'),
                    'status' => PostStatus::Open,
                ],
                [
                    'title' => 'Metro çıkışı çevresinde sabah güneşi altında görünmez sarı uyarı şeridi',
                    'user' => $u2,
                    'desc' => 'Araç sırasından zor seçildiği ifade edildi.',
                    'cat' => $catUl,
                    'district' => $kadikoyDistrict,
                    'lat' => 40.9963,
                    'lng' => 29.0364,
                    'status' => PostStatus::Open,
                ],
            ] as $extraDemo
        ) {
            $__img = $extraDemo['img'] ?? null;
            Post::query()->updateOrCreate(
                ['title' => $extraDemo['title']],
                [
                    'user_id' => $extraDemo['user']->id,
                    'description' => $extraDemo['desc'],
                    'media_url' => $__img,
                    'media' => $__img !== null ? [['type' => 'image', 'url' => $__img]] : null,
                    'type' => 'complaint',
                    'city_id' => $extraDemo['district']->city_id,
                    'district_id' => $extraDemo['district']->id,
                    'neighborhood_id' => isset($extraDemo['nh']) ? $extraDemo['nh']->id : null,
                    'latitude' => $extraDemo['lat'],
                    'longitude' => $extraDemo['lng'],
                    'category_id' => $extraDemo['cat']->id,
                    'institution_id' => isset($extraDemo['inst']) ? $extraDemo['inst']->id : null,
                    'status' => $extraDemo['status'],
                    'moderation_status' => PostModerationStatus::Approved,
                    'moderated_at' => now(),
                    'moderated_by_user_id' => $superAdmin->id,
                ],
            );
        }

        foreach (
            [
                ['title' => 'Nilüfer Görükle çevresinde kampüs–kent bağlantılı bisiklet şeridi eksikliği', 'user' => $damla, 'desc' => 'Öğrenci çıkış saatlerinde ana caddeye ani sıçrama gerekiyor.', 'cat' => $catUl, 'district' => $nilufer, 'lat' => 40.2267, 'lng' => 28.8439, 'img' => $demoImg('1558618047'), 'status' => PostStatus::Open],
                ['title' => 'Osmangazi Çekirge sıcaksuyu çevresinde kırık kaldırım taşı düzeni', 'user' => $cem, 'desc' => 'Yağmurda kenarı gevşemiş taşlar sürüş tehlikesi yaratıyor.', 'cat' => $catAlt, 'district' => $osmangazi, 'lat' => 40.1908, 'lng' => 29.0589, 'img' => $demoImg('1499793897398'), 'status' => PostStatus::InProgress],
                ['title' => 'Bursa merkez otobüs durağında engelli iniş alanı işgal bildirimi', 'user' => $kerem, 'desc' => 'Sabah saatlerinde ticari araç durması yüzünden rampaya erişim kesiliyor.', 'cat' => $catUl, 'district' => $osmangazi, 'lat' => 40.1839, 'lng' => 29.0612, 'status' => PostStatus::Open],
                ['title' => 'Muratpaşa Lara caddeperest işlekleri çöp toplanması sıklığı', 'user' => $selin, 'desc' => 'Yoğun turizm diliminde konteynerler dolunca yayılım oluşuyor.', 'cat' => $catCe, 'district' => $muratpasa, 'lat' => 36.8569, 'lng' => 30.7649, 'img' => $demoImg('1532996120744'), 'status' => PostStatus::Open],
                ['title' => 'Antalya Kepez’de park içi sulama kaçağı ve çamur oluşumu', 'user' => $u3, 'desc' => 'Çocuk oyun alanı kenarında günlerdir süren nem.', 'cat' => $catCe, 'district' => $kepez, 'lat' => 36.9316, 'lng' => 30.7138, 'img' => $demoImg('1416879595522'), 'status' => PostStatus::Open],
                ['title' => 'Muratpaşa’da gece market müziği ve komşuluk bildirimi', 'user' => $omer, 'desc' => '23:00 sonrası hoparlör yayını apartman bloğuna yansıyor.', 'cat' => $catGur, 'district' => $muratpasa, 'lat' => 36.8877, 'lng' => 30.7056, 'status' => PostStatus::Open],
                ['title' => 'Adana Seyhan Kuruköprü yakını kasis işaretlemesi solması', 'user' => $u2, 'desc' => 'Gece hız dalgalanması yaşanıyor; yaya geçidi yakını.', 'cat' => $catUl, 'district' => $seyhan, 'lat' => 37.0014, 'lng' => 35.3218, 'img' => $demoImg('1449824913935'), 'status' => PostStatus::InProgress],
                ['title' => 'Çukurova Balcalı yolu yan tarafında yanmış kablo kanalı kapağı', 'user' => $u1, 'desc' => 'Kapak yerinden kalkmış; uyarı konisi düşmüş.', 'cat' => $catGun, 'district' => $cukurovaIlce, 'lat' => 37.0517, 'lng' => 35.2848, 'img' => $demoImg('1581092160569-8929eaf89d5d'), 'status' => PostStatus::Open],
                ['title' => 'Adana Büyükşehir önü trafik yoğunluğu ve durak sırası düzeni', 'user' => $damla, 'desc' => 'Sabah 08:15 civarı üç araçlık sıra minibüs girişini kesiyor.', 'cat' => $catUl, 'district' => $seyhan, 'lat' => 37.0189, 'lng' => 35.3289, 'status' => PostStatus::Open],
                ['title' => 'Konya Selçuklu Alaaddin çevresi tarihi kaldırım bakımı talebi', 'user' => $kerem, 'desc' => 'Kaygan yüzey ve gevşek taşlar turistik güzergâhta.', 'cat' => $catAlt, 'district' => $selcuklu, 'lat' => 37.8749, 'lng' => 32.4938, 'img' => $demoImg('1469474968028'), 'status' => PostStatus::Open],
                ['title' => 'Meram Yaylapınar bağlantısında sokak lambası senkop bildirimi', 'user' => $selin, 'desc' => 'Üç direk aynı anda gidip geliyor; nöbetçi bildirildi.', 'cat' => $catAlt, 'district' => $meram, 'lat' => 37.8328, 'lng' => 32.4549, 'status' => PostStatus::Open],
                ['title' => 'Selçuklu tramvay çıkışında bisiklet parkı doluluğu ve düzensizlik', 'user' => $cem, 'desc' => 'İşlek saatlerde bağlantı direği önü işgal görülüyor.', 'cat' => $catUl, 'district' => $selcuklu, 'lat' => 37.8742, 'lng' => 32.4819, 'img' => $demoImg('1485965120188'), 'status' => PostStatus::Open],
                ['title' => 'Gaziantep Şahinbey tarihi sokakta daralan yangın geçiş hattı', 'user' => $omer, 'desc' => 'Önlük tezgâh genişlemesi son iki haftada daralmış.', 'cat' => $catGun, 'district' => $sahinbey, 'lat' => 37.0665, 'lng' => 37.3789, 'img' => $demoImg('1506905929245'), 'status' => PostStatus::Open],
                ['title' => 'Şehitkamil OSB yakını gürültü şikâyeti — kompresör süreleri', 'user' => $damla, 'desc' => 'Akşam 22:00 sonrası da sürdüğü komşu işletmeden.', 'cat' => $catGur, 'district' => $sehitkamil, 'lat' => 37.0924, 'lng' => 37.3412, 'status' => PostStatus::Open],
                ['title' => 'Şahinbey çöp toplama aralığı ve sızdıran konteyner', 'user' => $u3, 'desc' => 'Hafta sonu dolulukta sızdırma ve koku.', 'cat' => $catCe, 'district' => $sahinbey, 'lat' => 37.0598, 'lng' => 37.3715, 'img' => $demoImg('1530587191336'), 'status' => PostStatus::InProgress],
                ['title' => 'Trabzon Ortahisar Meydan Park yan giriş rampası bakımı', 'user' => $cem, 'desc' => 'Yağmur sonrası kayganlık artmış; kaydırmaz şerit solmuş.', 'cat' => $catAlt, 'district' => $ortahisar, 'lat' => 41.0054, 'lng' => 39.7308, 'img' => $demoImg('1518538188958'), 'status' => PostStatus::Open],
                ['title' => 'Ortahisar sahilden minibüs durakları sırası ve emniyet şeridi', 'user' => $u1, 'desc' => 'Öğle sıcağında emniyet şeridi üzerinde bekleyen yolcu yoğunluğu.', 'cat' => $catUl, 'district' => $ortahisar, 'lat' => 41.0018, 'lng' => 39.7198, 'status' => PostStatus::Open],
                ['title' => 'Trabzon Boztepe yolu çıkışında görüşü kesen çalı bakımı', 'user' => $kerem, 'desc' => 'Viraj çıkışında görüş üçgeni kapanmış.', 'cat' => $catUl, 'district' => $ortahisar, 'lat' => 41.0129, 'lng' => 39.7046, 'img' => $demoImg('1441974231531'), 'status' => PostStatus::Open],
                ['title' => 'Eskişehir Odunpazarı ara sokakta kopmuş yağmur oluğu kapağı', 'user' => $selin, 'desc' => 'Çocuk oyun alanına 30 m mesafede düşük kapak.', 'cat' => $catAlt, 'district' => $odunpazari, 'lat' => 39.7668, 'lng' => 30.5259, 'img' => $demoImg('1560497968646'), 'status' => PostStatus::Open],
                ['title' => 'Tepebaşı tram hattı çıkışında kaygan granit zemin uyarısı', 'user' => $omer, 'desc' => 'Sabah çiyinde iki kayma bildirimi aktarıldı.', 'cat' => $catGun, 'district' => $tepebasi, 'lat' => 39.7848, 'lng' => 30.4989, 'status' => PostStatus::Open],
                ['title' => 'Odunpazarı Çukurbaglar mahalle bakım çalıları kesimi zamanlaması', 'user' => $damla, 'desc' => 'Kuş yuvaları dönemi ile çakışmaması için plan talebi.', 'cat' => $catCe, 'district' => $odunpazari, 'lat' => 39.7712, 'lng' => 30.5318, 'status' => PostStatus::Open],
                ['title' => 'Mersin Yenişehir sahil yürüyüşünde gece aydınlatması kopması', 'user' => $u2, 'desc' => 'Üç direk üst üste yanmıyor; güvenlik için kayıt.', 'cat' => $catAlt, 'district' => $yeniehirMersin, 'lat' => 36.7859, 'lng' => 34.5829, 'img' => $demoImg('1500530855697'), 'status' => PostStatus::Open],
                ['title' => 'Yenişehir çevreyolu köprü altı birikinti temizliği', 'user' => $cem, 'desc' => 'Yağmur sonrası sel taşkını riski oluşturan moloz izleri.', 'cat' => $catCe, 'district' => $yeniehirMersin, 'lat' => 36.8129, 'lng' => 34.6289, 'status' => PostStatus::InProgress],
                ['title' => 'İstanbul Beşiktaş iskele çıkışı kalabalık sırasında kasis görünürlüğü', 'user' => $damla, 'desc' => 'Akşam vapur çıkışlarında kasis boyası silik.', 'cat' => $catUl, 'district' => $besiktasDistrict, 'lat' => 41.0422, 'lng' => 29.0049, 'img' => $demoImg('1540959733290'), 'status' => PostStatus::Open],
                ['title' => 'Ümraniye Çarşı metro çıkışı bisiklet park demiri gevşemesi', 'user' => $kerem, 'desc' => 'Vidalar çıkmış; kullanım güvenliği için bakım.', 'cat' => $catGun, 'district' => $umraniyeDistrict, 'lat' => 41.0269, 'lng' => 29.1189, 'status' => PostStatus::Open],
                ['title' => 'Ümraniye Alemdağ yolu üzerinde rutubet ve koku — kanal kapağı', 'user' => $selin, 'desc' => 'Konut girişine 15 m mesafede sürekli koku.', 'cat' => $catCe, 'district' => $umraniyeDistrict, 'lat' => 41.0359, 'lng' => 29.1018, 'img' => $demoImg('1584820927498'), 'status' => PostStatus::Open],
                ['title' => 'Nilüfer öğrenci yurtları hattında gece gürültü bildirimi', 'user' => $omer, 'desc' => 'Hafta içi 01:00 sonrası hoparlörlü etkinlik.', 'cat' => $catGur, 'district' => $nilufer, 'lat' => 40.2199, 'lng' => 28.8899, 'status' => PostStatus::Open],
                ['title' => 'Konya Meram mesire çevresi çöp kutusu sızdırması ve sinek yoğunluğu', 'user' => $u3, 'desc' => 'Hafta sonu ziyaretçi yoğunluğu ile birleşince rahatsızlık.', 'cat' => $catCe, 'district' => $meram, 'lat' => 37.8219, 'lng' => 32.4469, 'img' => $demoImg('1466697067136'), 'status' => PostStatus::Open],
                ['title' => 'Antalya Muratpaşa sahilde duyarlık tabelası önerisi — pet atığı', 'user' => $u1, 'desc' => 'Yoğun yürüyüş güzergâhında poşet dağılımı gözleniyor.', 'cat' => $catCe, 'district' => $muratpasa, 'lat' => 36.8849, 'lng' => 30.7069, 'status' => PostStatus::Open],
                ['title' => 'Gaziantep Şehitkamil spor kompleksi çevresi otopark düzeni', 'user' => $cem, 'desc' => 'Akşam antrenman çıkışında çıkmaz kalabalığı.', 'cat' => $catUl, 'district' => $sehitkamil, 'lat' => 37.0849, 'lng' => 37.3629, 'img' => $demoImg('1574259392081'), 'status' => PostStatus::Open],
            ] as $regionalDemo
        ) {
            $__img = $regionalDemo['img'] ?? null;
            Post::query()->updateOrCreate(
                ['title' => $regionalDemo['title']],
                [
                    'user_id' => $regionalDemo['user']->id,
                    'description' => $regionalDemo['desc'],
                    'media_url' => $__img,
                    'media' => $__img !== null ? [['type' => 'image', 'url' => $__img]] : null,
                    'type' => 'complaint',
                    'city_id' => $regionalDemo['district']->city_id,
                    'district_id' => $regionalDemo['district']->id,
                    'neighborhood_id' => null,
                    'latitude' => $regionalDemo['lat'],
                    'longitude' => $regionalDemo['lng'],
                    'category_id' => $regionalDemo['cat']->id,
                    'institution_id' => null,
                    'status' => $regionalDemo['status'],
                    'moderation_status' => PostModerationStatus::Approved,
                    'moderated_at' => now(),
                    'moderated_by_user_id' => $superAdmin->id,
                ],
            );
        }

        Story::query()->updateOrCreate(
            [
                'user_id' => $omer->id,
                'description' => 'Kordon’da gün doğumu — kısmen sis.',
            ],
            [
                'media_url' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d7?auto=format&fit=crop&w=420&q=80',
                'city_id' => $izmir->id,
                'district_id' => $konak->id,
                'expires_at' => now()->addHours(42),
            ],
        );

        $seedSupportBulk = static function (Campaign $c, iterable $users): void {
            foreach ($users as $ux) {
                CampaignSupporter::query()->firstOrCreate([
                    'campaign_id' => $c->id,
                    'user_id' => $ux->id,
                ]);
            }
        };

        $campaignHero = static fn (string $slug): string => '/img/stock/campaigns/'.md5($slug).'.jpg';

        $c1 = Campaign::query()->updateOrCreate(
            ['slug' => 'tek-ye-tekerlekli-erisim-ag'],
            [
                'user_id' => $u1->id,
                'title' => 'İstanbul ilçelerinde tekerlekli sandalye rampa güvenliği',
                'excerpt' => 'Kaldırım iniş çıkışlarında standart dışı eğimler toplanıyor; imza kampanyası.',
                'description' => "Türk mühendisleri ve hak savunucularıyla birlikte standart fotoğraf + GPS ile rapor oluşturuyoruz.\nKatılım yayında; her destek görünür imza olarak sayılıyor.",
                'hero_image_url' => $campaignHero('tek-ye-tekerlekli-erisim-ag'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 800,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(6),
            ],
        );
        $seedSupportBulk($c1, [$u1, $u2, $u3, $admin, $institutionUser, $kerem, $selin]);

        $c2 = Campaign::query()->updateOrCreate(
            ['slug' => 'geri-donusum-ataşehir-mahalleri'],
            [
                'user_id' => $u2->id,
                'title' => 'Ataşehir’de sıfır-atık puanı dostu kampanya',
                'excerpt' => 'Komşu bloklar arasında ayrıştırma istasyonu görünürlüğü için destek çağrısı.',
                'description' => "Komşuları bir araya getirip blok temsilcisi ile görüşeceğiz.\nDestek olanlar bildirildiğinde ilk buluşmaya davetliyiz.",
                'hero_image_url' => $campaignHero('geri-donusum-ataşehir-mahalleri'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 350,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $seedSupportBulk($c2, [$u1, $u2, $u3]);

        Campaign::query()->updateOrCreate(
            ['slug' => 'kentte-dijital-okuryazarlık-akademisi'],
            [
                'user_id' => $admin->id,
                'title' => 'Genel Türkiye: dijital okuryazarlık gönüllü akademisi',
                'excerpt' => 'Kentte yaşlı ve genç nüfus için yüz yüze küçük sınıflar için frekans araştırması.',
                'description' => 'Üniversiteli öğrencilerle birlikte pazar içi ve kahvehane odaklı ders taslağı paylaşıyoruz.',
                'hero_image_url' => $campaignHero('kentte-dijital-okuryazarlık-akademisi'),
                'city_id' => null,
                'goal_supporters' => 1200,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addYear(),
            ],
        );
        /** @var Campaign $c3 */
        $c3 = Campaign::query()->where('slug', 'kentte-dijital-okuryazarlık-akademisi')->firstOrFail();
        $seedSupportBulk($c3, [$u1, $u2, $u3, $admin, $kerem]);

        Campaign::query()->updateOrCreate(
            ['slug' => 'ankara-ilk-bahar-temizlik-gunu'],
            [
                'user_id' => $kerem->id,
                'title' => 'Çankaya’da gönüllü temizlik ve fidan hatırası',
                'excerpt' => 'Küçük bir parkta çöp miktarının azaltılması için toplumsal temizlik günü.',
                'description' => 'Katılımcı sayısı 40’ın üzerine çıktığında belediye temsilcisine bloklu rapor teslim.',
                'hero_image_url' => $campaignHero('ankara-ilk-bahar-temizlik-gunu'),
                'city_id' => $ankara->id,
                'goal_supporters' => 150,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(2),
            ],
        );
        $c4 = Campaign::query()->where('slug', 'ankara-ilk-bahar-temizlik-gunu')->firstOrFail();
        $seedSupportBulk($c4, [$kerem, $selin, $u3]);

        Campaign::query()->updateOrCreate(
            ['slug' => 'kampanya-model-bekliyor'],
            [
                'user_id' => $u3->id,
                'title' => 'Sokak aydınlatması için komşuluk bildirimi (taslak bekliyor)',
                'excerpt' => null,
                'description' => 'Henüz detay fotoğraf eklenecek; moderasyon sırasına girdi.',
                'hero_image_url' => $campaignHero('kampanya-model-bekliyor'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 50,
                'moderation_status' => CampaignModerationStatus::Pending,
                'moderated_at' => null,
                'moderated_by_user_id' => null,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );

        Campaign::query()->updateOrCreate(
            ['slug' => 'sr-kampanya-madde-paylasimi-redd'],
            [
                'user_id' => $u2->id,
                'title' => 'Reddedilmiş örnek: riskli bağış linkleri',
                'excerpt' => 'Platform politikası gereği örnek red kaydıdır.',
                'description' => 'Bu kampanya tasarı gereği doğrudan bağış bağlantıları içermekte olduğundan yayına uygun görülmedi.',
                'hero_image_url' => $campaignHero('sr-kampanya-madde-paylasimi-redd'),
                'city_id' => null,
                'goal_supporters' => 10,
                'moderation_status' => CampaignModerationStatus::Rejected,
                'moderated_at' => now()->subDays(4),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => 'Doğrudan bağış bağlantıları ve kişisel IBAN politikaya aykırı.',
                'ends_at' => null,
            ],
        );

        Campaign::query()->updateOrCreate(
            ['slug' => 'kent-guvenligi-farkindalik-dizisi'],
            [
                'user_id' => $selin->id,
                'title' => 'Gece yürüyüş güvenliği — fener ve bildir zinciri',
                'excerpt' => 'Kent merkezlerinde nöbetçi gönüllü haritasını birlikte genişleteceğiz.',
                'description' => 'Katılımcılar rota bildirerek haritaya ekleniyor.',
                'hero_image_url' => $campaignHero('kent-guvenligi-farkindalik-dizisi'),
                'city_id' => $ankara->id,
                'goal_supporters' => 480,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $c5 = Campaign::query()->where('slug', 'kent-guvenligi-farkindalik-dizisi')->firstOrFail();
        $seedSupportBulk($c5, [$u1, $u2, $u3, $kerem, $selin, $admin]);

        Campaign::query()->updateOrCreate(
            ['slug' => 'izmir-kent-suhatti-gonullu-musteri-duyusu'],
            [
                'user_id' => $omer->id,
                'title' => 'İzmir mahallelerinde su kesintisi bildirimin şeffaf loglanması',
                'excerpt' => 'Gönüllüler tek formatta bildiriminizi arşivliyor.',
                'description' => "Kırılmayan hatlar ve geç bildirilmiş kesintiler kayıtlanınca daha anlamlı hale gelecek.\nKurumlara paralel bildiriminizi yapmayın unutmayın.",
                'hero_image_url' => $campaignHero('izmir-kent-suhatti-gonullu-musteri-duyusu'),
                'city_id' => $izmir->id,
                'goal_supporters' => 920,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(5),
            ],
        );
        $c6 = Campaign::query()->where('slug', 'izmir-kent-suhatti-gonullu-musteri-duyusu')->firstOrFail();
        $seedSupportBulk($c6, [$omer, $damla, $cem, $u3, $selin]);

        Campaign::query()->updateOrCreate(
            ['slug' => 'istanbul-kadikoy-kentsel-agac-etiketi-haritasi'],
            [
                'user_id' => $damla->id,
                'title' => 'Kadıköy’de tarihi ağaçlar için halk dostu fotoğraf kataloğu kampanyası',
                'excerpt' => 'Bilim insanlarıyla birlikte etiketi okunabilir ağaç haritasına destek olun.',
                'description' => "Her destek fotoğraf alanı güven kurallarına uygun fotoğraf yüklemenizi hatırlatır.\nKent belleği için kolektif albüm çıkarılıyor.",
                'hero_image_url' => $campaignHero('istanbul-kadikoy-kentsel-agac-etiketi-haritasi'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 640,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $c7 = Campaign::query()->where('slug', 'istanbul-kadikoy-kentsel-agac-etiketi-haritasi')->firstOrFail();
        $seedSupportBulk($c7, [$damla, $u1, $u2, $kerem, $admin, $cem]);

        $c8 = Campaign::query()->updateOrCreate(
            ['slug' => 'sma-erisilebilir-saglik-farkindalik'],
            [
                'user_id' => $u1->id,
                'title' => 'SMA ve nadir hastalıklarda erişilebilir sağlık hizmeti farkındalığı',
                'excerpt' => 'Muayene, görüntüleme ve multidisipliner takip süreçlerinde engelsiz erişim için dayanışma çağrısı.',
                'description' => "Spinal Musküler Atrofi (SMA) ve benzeri nadir hastalıklarda zamanında değerlendirme çoğu zaman hayati önem taşır.\nBu kampanya; hastanelerde fiziksel erişim, iletişim desteği ve süreç bilgisinin herkes için anlaşılır olması için görünürlük oluşturmayı amaçlar.\nResmi başvuru ve tedavi kararları yalnızca ilgili sağlık kuruluşları ile yürütülür; burada yalnızca hak temelli farkındalık ve şeffaflık hedeflenir.",
                'hero_image_url' => $campaignHero('sma-erisilebilir-saglik-farkindalik'),
                'city_id' => null,
                'goal_supporters' => 5000,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(8),
            ],
        );
        $seedSupportBulk($c8, [$u1, $u2, $u3, $kerem, $selin, $damla, $admin]);

        $c9 = Campaign::query()->updateOrCreate(
            ['slug' => 'sma-okul-ulasim-ve-rehabilitasyon-gorunurlugu'],
            [
                'user_id' => $selin->id,
                'title' => 'SMA ile yaşayan çocuklar için okul, ulaşım ve rehabilitasyon görünürlüğü',
                'excerpt' => 'Eğitimde makul düzenleme, güvenli okul servisi ve terapi sürekliliği için imza ve bilgi paylaşımı.',
                'description' => "Çocukların okula erişimi ve günlük rehabilitasyon programları aileler için yoğun organizasyon gerektirir.\nBu kampanya; kamusal alanda rampa ve asansör kullanımı, okul içi destek hizmetleri ve mahalle ölçeğinde duyarlılık oluşturmayı hedefler.\nBağış veya kişisel yardım toplama yerine yalnızca politika ve erişilebilirlik konuşması yapılır.",
                'hero_image_url' => $campaignHero('sma-okul-ulasim-ve-rehabilitasyon-gorunurlugu'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 3200,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(5),
            ],
        );
        $seedSupportBulk($c9, [$u1, $u2, $u3, $omer, $damla]);

        $c10 = Campaign::query()->updateOrCreate(
            ['slug' => 'sma-nadir-hastalik-arastirma-seffafligi'],
            [
                'user_id' => $kerem->id,
                'title' => 'Nadir hastalıklarda araştırma ve tedavi erişimi şeffaflığı (SMA odaklı)',
                'excerpt' => 'Klinik süreçlerin anlaşılır dilde özetlenmesi ve güvenilir kaynaklara yönlendirme için kolektif destek.',
                'description' => "SMA alanında bilimsel gelişmeler hızlıdır; aileler ve yakınları doğru bilgiye ulaşmakta zorlanabilir.\nBu kampanya; resmi sağlık otoriteleri ve özetleyici içerikler üzerinden yanlış bilginin azaltılmasına katkı hedefler.\nKişisel bağış linki veya ilaç temini vaadi içermez; yalnızca şeffaf bilgi ve hak odaklı iletişimi güçlendirir.",
                'hero_image_url' => $campaignHero('sma-nadir-hastalik-arastirma-seffafligi'),
                'city_id' => $ankara->id,
                'goal_supporters' => 4100,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $seedSupportBulk($c10, [$kerem, $admin, $u2, $u3, $cem]);

        $c11 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-antalya-erisilebilir-kiyi-farkindalik'],
            [
                'user_id' => $damla->id,
                'title' => 'Antalya kıyılarında erişilebilirlik ve çevre duyarlığı',
                'excerpt' => 'Sahil güzergâhlarında rampa, işaret ve atık bilinci için görünürlük.',
                'description' => "Turizm yoğun bölgelerde erişilebilir erişim noktalarının haritalanması ve çevre dostu davranışların yaygınlaştırılması hedeflenir.\nBağış veya kişisel yardım talebi içermez; şeffaf bilgi ve kolektif farkındalık odaklıdır.",
                'hero_image_url' => $campaignHero('csr-antalya-erisilebilir-kiyi-farkindalik'),
                'city_id' => $antalya->id,
                'goal_supporters' => 880,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(7),
            ],
        );
        $seedSupportBulk($c11, [$damla, $omer, $selin, $u1, $admin]);

        $c12 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-bursa-nilufer-kadin-emek-dayanismasi'],
            [
                'user_id' => $selin->id,
                'title' => 'Bursa Nilüfer’de kadın emeği ve kooperatif görünürlüğü',
                'excerpt' => 'Yerel üretim ve komşuluk ekonomisi için şeffaf dayanışma çağrısı.',
                'description' => "Komşu pazarları ve küçük üretici buluşmalarının görünür olması; hak odaklı iletişim ile desteklenir.\nPlatform politikası gereği doğrudan bağış veya IBAN içermez.",
                'hero_image_url' => $campaignHero('csr-bursa-nilufer-kadin-emek-dayanismasi'),
                'city_id' => $bursa->id,
                'goal_supporters' => 620,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $seedSupportBulk($c12, [$selin, $damla, $kerem, $cem, $u2]);

        $c13 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-adana-cevre-ve-yangin-bilinclendirme'],
            [
                'user_id' => $omer->id,
                'title' => 'Adana’da sıcak hava ve çevre riskleri için bilinç paylaşımı',
                'excerpt' => 'Yeşil alan kullanımı ve güvenli yakım bilinci için kolektif duyuru.',
                'description' => 'Kent sıcağı ve çevre hassasiyetleri konusunda güvenilir kaynaklara yönlendirme ve mahalle ölçeğinde farkındalık hedeflenir.',
                'hero_image_url' => $campaignHero('csr-adana-cevre-ve-yangin-bilinclendirme'),
                'city_id' => $adana->id,
                'goal_supporters' => 540,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(4),
            ],
        );
        $seedSupportBulk($c13, [$omer, $u3, $admin, $kerem]);

        $c14 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-konya-kultur-miras-gonulluleri'],
            [
                'user_id' => $kerem->id,
                'title' => 'Konya’da kültürel miras ve mahalle hafızası gönüllülüğü',
                'excerpt' => 'Tarihi güzergâhlarda saygılı ziyaret ve dokunuş bilinci.',
                'description' => 'Kent dokusunun korunmasına katkı için fotoğraf ve gözlem paylaşımını teşvik eden kolektif bir görünürlük kampanyasıdır.',
                'hero_image_url' => $campaignHero('csr-konya-kultur-miras-gonulluleri'),
                'city_id' => $konya->id,
                'goal_supporters' => 730,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(10),
            ],
        );
        $seedSupportBulk($c14, [$kerem, $selin, $u1, $damla, $cem]);

        $c15 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-gaziantep-kulturel-dayanisma-kopruleri'],
            [
                'user_id' => $cem->id,
                'title' => 'Gaziantep’te kültürel çeşitlilik ve komşuluk diyaloğu',
                'excerpt' => 'Ortak alanlarda saygı ve sürdürülebilir kent yaşamı için çağrı.',
                'description' => 'Mahalle buluşmaları ve kültürel etkinlik görünürlüğünü destekleyen politikadan bağımsız bir iletişim çerçevesidir.',
                'hero_image_url' => $campaignHero('csr-gaziantep-kulturel-dayanisma-kopruleri'),
                'city_id' => $gaziantep->id,
                'goal_supporters' => 690,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $seedSupportBulk($c15, [$cem, $omer, $u2, $u3, $institutionUser]);

        $c16 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-trabzon-yayla-temizligi-genclik'],
            [
                'user_id' => $damla->id,
                'title' => 'Trabzon yaylalarında doğaya saygılı ziyaret gençlik kampanyası',
                'excerpt' => 'Atıksız güzergâh ve güvenli iniş çıkış bilinci.',
                'description' => 'Yayla güzergâhlarında çevresel duyarlılık ve güvenlik için kolektif hatırlatmalar paylaşılır.',
                'hero_image_url' => $campaignHero('csr-trabzon-yayla-temizligi-genclik'),
                'city_id' => $trabzon->id,
                'goal_supporters' => 510,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(3),
            ],
        );
        $seedSupportBulk($c16, [$damla, $kerem, $admin, $selin]);

        $c17 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-eskisehir-yesil-ulasim-komsuluk'],
            [
                'user_id' => $selin->id,
                'title' => 'Eskişehir’de bisiklet ve toplu taşıma dostu komşuluk',
                'excerpt' => 'Durak çevresi güvenliği ve paylaşımlı güzergâh farkındalığı.',
                'description' => 'Öğrenci ve çalışanların günlük ulaşımında güvenli bekleyiş ve şeffaf geri bildirim kültürünü güçlendirir.',
                'hero_image_url' => $campaignHero('csr-eskisehir-yesil-ulasim-komsuluk'),
                'city_id' => $eskisehir->id,
                'goal_supporters' => 640,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(6),
            ],
        );
        $seedSupportBulk($c17, [$selin, $cem, $u1, $omer]);

        $c18 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-mersin-akdeniz-atik-azaltma'],
            [
                'user_id' => $omer->id,
                'title' => 'Mersin sahil şeridinde plastik azaltma ve bilinç paylaşımı',
                'excerpt' => 'Tek kullanımlık azaltımı ve geri dönüşüm görünürlüğü.',
                'description' => 'Kent sahillerinde çevre dostu davranışların yaygınlaştırılması için kolektif destek ve şeffaf iletişim hedeflenir.',
                'hero_image_url' => $campaignHero('csr-mersin-akdeniz-atik-azaltma'),
                'city_id' => $mersin->id,
                'goal_supporters' => 560,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(5),
            ],
        );
        $seedSupportBulk($c18, [$omer, $damla, $u3, $kerem, $u2]);

        $c19 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-turkiye-deprem-hazirlik-farkindalik'],
            [
                'user_id' => $admin->id,
                'title' => 'Türkiye geneli: deprem çantası ve aile planı farkındalığı',
                'excerpt' => 'Resmi kurum özetleriyle uyumlu, yanlış bilgiyi azaltan kolektif hatırlatma.',
                'description' => "Afet hazırlığında mahalle ve aile ölçeğinde yapılacaklar için güvenilir içeriklere yönlendirme yapılır.\nKişisel bağış talebi içermez.",
                'hero_image_url' => $campaignHero('csr-turkiye-deprem-hazirlik-farkindalik'),
                'city_id' => null,
                'goal_supporters' => 12000,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addYear(),
            ],
        );
        $seedSupportBulk($c19, [$admin, $u1, $u2, $u3, $kerem, $selin, $omer, $damla]);

        $depremYorumlari = [
            [$selin, 'Evde deprem çantası eksikti; buradaki hatırlatma sayesinde eksikleri tamamladım. Teşekkürler.'],
            [$u1, 'Apartmandayız, yönetimle tahliye buluşma noktası için bileşeni açtık — kampanya bunu tetikledi.'],
            [$omer, 'Çocuklara sakin kalmayı ve iletişim planını oyunla anlattık. Böyle içerik çok değerli.'],
            [$damla, 'Aile grubunda “deprem anı” iletişim sırasını netleştirdik; basit ama kritik.'],
            [$kerem, 'Dolaşan yanlış bilgi çok; yalnızca güvenilir kaynaklara temas etmesi güven verici.'],
            [$cem, 'Deprem sigortası ve protokol için kısa yönlendirme notu eklersen harika olur.'],
            [$u2, 'Destek verdim — daha çok kişiye ulaşmasını dilerim.'],
            [$u3, 'Veli grubunda paylaştım, “işe yaradı” dönüşü aldık.'],
        ];
        foreach ($depremYorumlari as $i => [$yorumUser, $text]) {
            CampaignComment::query()->firstOrCreate(
                [
                    'campaign_id' => $c19->id,
                    'user_id' => $yorumUser->id,
                    'content' => $text,
                ],
                [
                    'created_at' => now()->subHours(8 + $i * 5),
                    'updated_at' => now()->subHours(8 + $i * 5),
                ],
            );
        }

        $c20 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-turkiye-kan-bagisi-haftasi-dayanisma'],
            [
                'user_id' => $u1->id,
                'title' => 'Kan bağışı bilinci ve güvenli bağış noktası görünürlüğü',
                'excerpt' => 'Resmi sağlık birimleriyle uyumlu hatırlatma ve önyargı azaltma.',
                'description' => 'İnsani dayanışma için kan bağışının önemini anlatır; yönlendirmeler yalnızca kamusal ve güvenilir kaynaklara yapılır.',
                'hero_image_url' => $campaignHero('csr-turkiye-kan-bagisi-haftasi-dayanisma'),
                'city_id' => null,
                'goal_supporters' => 8500,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(9),
            ],
        );
        $seedSupportBulk($c20, [$u1, $u2, $admin, $cem, $damla, $institutionUser]);

        $c21 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-istanbul-kadin-dayanisma-gorunurlugu'],
            [
                'user_id' => $damla->id,
                'title' => 'İstanbul’da kadın dayanışması ve güvenli kamusal alan görünürlüğü',
                'excerpt' => 'Gece güzergâhları ve ulaşım güvenliği için kolektif duyarlılık.',
                'description' => 'Kent yaşamında güvenli erişim ve şeffaf bildirim kültürünü güçlendiren sosyal sorumluluk kampanyasıdır.',
                'hero_image_url' => $campaignHero('csr-istanbul-kadin-dayanisma-gorunurlugu'),
                'city_id' => $istanbul->id,
                'goal_supporters' => 4200,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => now()->addMonths(8),
            ],
        );
        $seedSupportBulk($c21, [$damla, $selin, $u3, $u2, $kerem]);

        $c22 = Campaign::query()->updateOrCreate(
            ['slug' => 'csr-genclik-mentorluk-kopruleri-tr'],
            [
                'user_id' => $admin->id,
                'title' => 'Gençlik mentorluk köprüleri — şeffaf gönüllülük ve güvenli iletişim',
                'excerpt' => 'Üniversite–kent paylaşımında kurumsal olmayan güvenli buluşma ilkeleri.',
                'description' => "Mentorluk ve gönüllülük süreçlerinde şeffaflık ve güvenli iletişim ilkelerini yaygınlaştırmayı amaçlar.\nDoğrudan para veya bağlantı talebi içermez.",
                'hero_image_url' => $campaignHero('csr-genclik-mentorluk-kopruleri-tr'),
                'city_id' => null,
                'goal_supporters' => 6700,
                'moderation_status' => CampaignModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $superAdmin->id,
                'moderation_note' => null,
                'ends_at' => null,
            ],
        );
        $seedSupportBulk($c22, [$admin, $kerem, $cem, $omer, $u3]);

        $ornekSikayetYorumlari = [
            'Aynı bölgedenim, destekliyorum; umarım kısa sürede çözülür.',
            'Fotoğraf ve konum net, teşekkürler — ben de dikkat edeceğim.',
            'Belediye ekiplerine iletilmesi için paylaştım.',
            'Buradan da geçiyorum, risk gerçekten var.',
            'Komşu grubunda da konu açıldı; görünür olması iyi.',
            'Ben de destek verdim; çözüm sürecini takip edeceğim.',
            'Yağmurlu günde daha da kötüleşiyor, dikkat çekmişsiniz.',
            'Engelli erişimi açısından kritik bir nokta, katılıyorum.',
            'Sabah trafiğinde fark etmiştim, kayıt için teşekkürler.',
            'Kısa sürede bakım yapılırsa sevinirim.',
            'İyi ki bildirmişsiniz; biz de şikâyet oluşturduk.',
            'Topluluk olarak destekliyorum.',
            'Kurumun yanıt vermesini umuyorum.',
            'Aynı mağduriyeti yaşayan biri olarak dayanışma.',
            'Güvenlik için önceliklendirilmeli.',
            'Çevreye duyarlı yaklaşım önemli, elinize sağlık.',
            'Harita üzerinden konumu net görmek faydalı.',
            'Yorumları okudum; çoğumuz aynı fikirde.',
            'Kent yaşamında şeffaflık için önemli bir paylaşım.',
            'Burada yaşayan herkesin günlük güzergâhı — çözüm bekliyoruz.',
        ];

        $yorumYazanlarSikayet = collect([$u1, $u2, $u3, $kerem, $selin, $omer, $damla, $cem, $admin])->filter();
        foreach (Post::query()->publicApproved()->orderByDesc('id')->limit(48)->cursor() as $px) {
            $eklenen = 0;
            foreach ($yorumYazanlarSikayet->shuffle()->all() as $yx) {
                if ((int) $yx->id === (int) $px->user_id) {
                    continue;
                }
                $icerik = $ornekSikayetYorumlari[($px->id + $eklenen) % count($ornekSikayetYorumlari)];
                Comment::query()->firstOrCreate(
                    [
                        'post_id' => $px->id,
                        'user_id' => $yx->id,
                        'content' => $icerik,
                    ],
                    []
                );
                $eklenen++;
                if ($eklenen >= 3) {
                    break;
                }
            }
        }

        $kampanyaYorumPaketi = [
            [$c1, $u2, 'Rampalar konusunda çok haklısınız; okul güzergâhımızda da sıkıntı var.'],
            [$c1, $selin, 'İmza attım; mecliste gündeme gelmesini dilerim.'],
            [$c2, $kerem, 'Atık istasyonu fikrini komşularla konuştuk, olumlu dönüşler aldık.'],
            [$c2, $damla, 'Blok yönetimine ilettim, görüşme ayarlanacak.'],
            [$c3, $omer, 'Ülke geneli kampanya olması güzel; bizim ilde de gönüllü olmak isterim.'],
            [$c4, $u3, 'Temizlik gününe katılmak için bilgi bekliyoruz.'],
            [$c5, $cem, 'Gece yürüyüş güvenliği için harita fikri çok pratik.'],
            [$c6, $u1, 'Su kesintisi kayıtlarının tek formatta toplanması çok ihtiyaç.'],
            [$c6, $damla, 'Kent genelinden de destekliyorum.'],
            [$c7, $kerem, 'Ağaç etiketi çalışması bilimsel açıdan değerli, teşekkürler.'],
            [$c8, $selin, 'Hastane erişimi konusunda içerikler açıklayıcı.'],
            [$c9, $u2, 'Rehabilitasyon sürekliliği zorlayıcı; görünür olması önemli.'],
            [$c10, $admin, 'Kaynak yönlendirmesi sade dilde, çok iyi.'],
            [$c11, $omer, 'Sahil erişimi için farkındalık artmalı, destek verdim.'],
            [$c12, $damla, 'Kooperatif modelleri yerel ekonomiye iyi gelir.'],
            [$c13, $cem, 'Sıcak havada risk konusunda bilinçlenmek şart.'],
            [$c14, $u1, 'Kültürel miras gönüllüsüyüm; kampanyayı paylaştım.'],
            [$c15, $selin, 'Komşuluk diyaloğu her şehrin ihtiyacı.'],
            [$c16, $kerem, 'Yaylada çöpsüz güzergâh için dikkat edeceğiz.'],
            [$c17, $u3, 'Bisiklet hattı ve durak güvenliği çok yerinde.'],
            [$c18, $omer, 'Sahilde plastik azaltımını okulda anlattık.'],
            [$c20, $u2, 'Kan bağışı konusunda doğru bilgi çok değerli.'],
            [$c21, $damla, 'Güvenli kamusal alan herkesin hakkı.'],
            [$c22, $kerem, 'Gönüllülükte şeffaflık şart, katılıyorum.'],
        ];

        foreach ($kampanyaYorumPaketi as [$cmp, $usr, $txt]) {
            if (! $cmp->isPubliclyApproved()) {
                continue;
            }
            CampaignComment::query()->firstOrCreate(
                [
                    'campaign_id' => $cmp->id,
                    'user_id' => $usr->id,
                    'content' => $txt,
                ],
                []
            );
        }

        $sf = static fn (string $pid): string => 'https://images.unsplash.com/photo-'.$pid.'?auto=format&fit=crop&w=480&q=78';
        $hikayeSatirlari = [
            [$u1, 'Vapur iskelesinde gün batımı — Kadıköy.', $sf('1519451241324'), $istanbul->id, $kadikoyDistrict->id, 40.99, 29.03],
            [$u2, 'Moda sahilde tempolu yürüyüş bandı çok keyifli.', $sf('1470071459604'), $istanbul->id, $kadikoyDistrict->id, 40.978, 29.025],
            [$u3, 'Beşiktaş çarşının arka sokaklarında taze simit kokusu.', $sf('1504754524776'), $istanbul->id, $besiktasDistrict->id, 41.04, 29.01],
            [$kerem, 'Ümraniye metro çıkışında çiçek tezgâhı açılmış.', $sf('1490750967868'), $istanbul->id, $umraniyeDistrict->id, 41.03, 29.11],
            [$selin, 'Ataşehir parkında çocuk oyun alanı yenilenmiş.', $sf('1502082555484'), $istanbul->id, $atasehir->id, 40.98, 29.12],
            [$omer, 'İçerenköy’de sabah sisinde sakin sokak.', $sf('1503676260728'), $istanbul->id, $atasehir->id, 40.977, 29.10],
            [$damla, 'Kızılay’da kitapçı vitrinleri güzel.', $sf('1524993667549'), $ankara->id, $cankaya->id, 39.92, 32.86],
            [$cem, 'Çankaya’da yeni banklar gölgelikli olmuş.', $sf('1489515217757'), $ankara->id, $cankaya->id, 39.91, 32.84],
            [$admin, 'Bahçeli’te tramvay penceresinden kış manzarası.', $sf('1540959733290'), $ankara->id, $cankaya->id, 39.905, 32.84],
            [$u1, 'Alsancak’ta kahve molası — martı sesleri.', $sf('1506905929245'), $izmir->id, $konak->id, 38.44, 27.14],
            [$u2, 'Bornova kampüs yolunda erguvan ağaçları.', $sf('1464828026166'), $izmir->id, $bornovaDistrict->id, 38.46, 29.22],
            [$u3, 'Konak’ta tramvay durağı kalabalığı ama düzenli.', $sf('1512424458315'), $izmir->id, $konak->id, 38.42, 27.13],
            [$kerem, 'Nilüfer Görükle’de kampüs gün batımı.', $sf('1464859139713'), $bursa->id, $nilufer->id, 40.23, 28.84],
            [$selin, 'Osmangazi’de tarihi çarşı aralığı dar ama şirin.', $sf('1519681393784'), $bursa->id, $osmangazi->id, 40.19, 29.06],
            [$omer, 'Muratpaşa Lara’da deniz meltemi.', $sf('1507525428033'), $antalya->id, $muratpasa->id, 36.86, 30.76],
            [$damla, 'Kepez’de park içi çocuk gülüşleri.', $sf('1502086226184'), $antalya->id, $kepez->id, 36.93, 29.71],
            [$cem, 'Adana Seyhan’da portakal ağacı gölgesi.', $sf('1519682333338'), $adana->id, $seyhan->id, 37.00, 35.32],
            [$u1, 'Çukurova geniş bulvarında akşam yürüyüşü.', $sf('1497366215478'), $adana->id, $cukurovaIlce->id, 37.05, 35.28],
            [$u2, 'Konya Alaaddin çevresinde sakin bir öğle.', $sf('1469474968028'), $konya->id, $selcuklu->id, 37.87, 32.49],
            [$u3, 'Meram mesire havası serin geldi bugün.', $sf('1441974231531'), $konya->id, $meram->id, 37.83, 32.45],
            [$kerem, 'Gaziantep taş konak sokaklarında fotoğraf turu.', $sf('1582719418884'), $gaziantep->id, $sahinbey->id, 37.07, 37.38],
            [$selin, 'Şehitkamil’de spor kompleksi önü kalabalık.', $sf('1571902943202'), $gaziantep->id, $sehitkamil->id, 37.09, 37.34],
            [$omer, 'Trabzon Boztepe sisinden Karadeniz.', $sf('1518538188958'), $trabzon->id, $ortahisar->id, 41.01, 39.73],
            [$damla, 'Ortahisar meydanda akşam çayı keyfi.', $sf('1449459185969'), $trabzon->id, $ortahisar->id, 41.00, 39.72],
            [$cem, 'Eskişehir Porsuk kenarında ördekler.', $sf('1560497968646'), $eskisehir->id, $odunpazari->id, 39.77, 30.53],
            [$u1, 'Tepebaşı tramvay istasyonu çıkışı kalabalık.', $sf('1577081331882'), $eskisehir->id, $tepebasi->id, 39.78, 30.52],
            [$u2, 'Mersin sahilinde kısa koşu molası.', $sf('1500530855697'), $mersin->id, $yeniehirMersin->id, 36.79, 34.58],
            [$u3, 'Yenişehir sahil parkında bisiklet sürdük.', $sf('1558618666'), $mersin->id, $yeniehirMersin->id, 36.81, 34.63],
        ];

        foreach ($hikayeSatirlari as $hi => [$su, $desc, $media, $cid, $did, $slat, $slng]) {
            Story::query()->updateOrCreate(
                [
                    'user_id' => $su->id,
                    'description' => $desc,
                ],
                [
                    'media_url' => $media,
                    'city_id' => $cid,
                    'district_id' => $did,
                    'latitude' => $slat,
                    'longitude' => $slng,
                    'expires_at' => now()->addHours(20 + ($hi % 48)),
                ],
            );
        }

        foreach ([$c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10, $c11, $c12, $c13, $c14, $c15, $c16, $c17, $c18, $c19, $c20, $c21, $c22] as $__c) {
            $__c->forceFill(['supporter_count' => $__c->supporters()->count()])->saveQuietly();
        }

        $this->command?->info('Örnek veriler eklendi. Süper admin: erkanulker0@gmail.com (kurulum şifreniz DatabaseSeeder’da tanımlı).');
        $this->command?->line('Admin: +905530000001 (password) | Kurum: +905530000002 | Vatandaş: +905530000003–005');
    }
}
