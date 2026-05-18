<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Üretim ortamı için minimum veri: şikâyet kategorileri.
 * Demo kullanıcı / örnek şikâyet için DatabaseSeeder kullanın (yalnızca geliştirme).
 */
class EssentialDataSeeder extends Seeder
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

        $this->command?->info('Temel kategoriler yüklendi ('.count($categories).').');

        $this->call(CampaignTopicSeeder::class);
    }
}
