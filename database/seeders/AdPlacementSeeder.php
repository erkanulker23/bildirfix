<?php

namespace Database\Seeders;

use App\Models\AdPlacement;
use Illuminate\Database\Seeder;

class AdPlacementSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key' => 'feed_sidebar', 'label' => 'Akış — yan sütun', 'sort_order' => 10],
            ['key' => 'feed_inline', 'label' => 'Akış — gönderi arası', 'sort_order' => 20],
            ['key' => 'city_top', 'label' => 'İl sayfası — üst', 'sort_order' => 30],
            ['key' => 'home_mid', 'label' => 'Ana sayfa — orta', 'sort_order' => 40],
        ];

        foreach ($rows as $row) {
            AdPlacement::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'label' => $row['label'],
                    'type' => 'adsense',
                    'is_active' => false,
                    'sort_order' => $row['sort_order'],
                ],
            );
        }
    }
}
