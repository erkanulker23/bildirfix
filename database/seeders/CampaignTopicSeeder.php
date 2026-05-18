<?php

namespace Database\Seeders;

use App\Models\CampaignTopic;
use Illuminate\Database\Seeder;

class CampaignTopicSeeder extends Seeder
{
    public function run(): void
    {
        $order = 0;
        foreach (config('campaign_topics.items', []) as $item) {
            $order += 10;
            CampaignTopic::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'group_key' => $item['group'],
                    'sort_order' => $order,
                ],
            );
        }
    }
}
