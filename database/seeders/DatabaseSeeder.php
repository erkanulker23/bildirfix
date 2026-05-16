<?php

namespace Database\Seeders;

use App\Enums\CampaignModerationStatus;
use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Campaign;
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

        $c1 = Campaign::query()->updateOrCreate(
            ['slug' => 'tek-ye-tekerlekli-erisim-ag'],
            [
                'user_id' => $u1->id,
                'title' => 'İstanbul ilçelerinde tekerlekli sandalye rampa güvenliği',
                'excerpt' => 'Kaldırım iniş çıkışlarında standart dışı eğimler toplanıyor; imza kampanyası.',
                'description' => "Türk mühendisleri ve hak savunucularıyla birlikte standart fotoğraf + GPS ile rapor oluşturuyoruz.\nKatılım yayında; her destek görünür imza olarak sayılıyor.",
                'hero_image_url' => 'https://images.unsplash.com/photo-1576765608535-5f044d774c66?auto=format&fit=crop&w=900&q=80',
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
                'hero_image_url' => null,
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
                'description' => "Üniversiteli öğrencilerle birlikte pazar içi ve kahvehane odaklı ders taslağı paylaşıyoruz.",
                'hero_image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
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
                'description' => "Katılımcı sayısı 40’ın üzerine çıktığında belediye temsilcisine bloklu rapor teslim.",
                'hero_image_url' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&w=900&q=80',
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
                'description' => "Henüz detay fotoğraf eklenecek; moderasyon sırasına girdi.",
                'hero_image_url' => null,
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
                'description' => "Bu kampanya tasarı gereği doğrudan bağış bağlantıları içermekte olduğundan yayına uygun görülmedi.",
                'hero_image_url' => null,
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
                'description' => "Katılımcılar rota bildirerek haritaya ekleniyor.",
                'hero_image_url' => 'https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?auto=format&fit=crop&w=900&q=80',
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
                'hero_image_url' => 'https://images.unsplash.com/photo-1500375592092-40eb4368fce0?auto=format&fit=crop&w=900&q=80',
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
                'hero_image_url' => 'https://images.unsplash.com/photo-1441974231531-a622851c4d93?auto=format&fit=crop&w=900&q=80',
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

        foreach ([$c1, $c2, $c3, $c4, $c5, $c6, $c7] as $__c) {
            $__c->forceFill(['supporter_count' => $__c->supporters()->count()])->saveQuietly();
        }

        $this->command?->info('Örnek veriler eklendi. Süper admin: erkanulker0@gmail.com (kurulum şifreniz DatabaseSeeder’da tanımlı).');
        $this->command?->line('Admin: +905530000001 (password) | Kurum: +905530000002 | Vatandaş: +905530000003–005');
    }
}
