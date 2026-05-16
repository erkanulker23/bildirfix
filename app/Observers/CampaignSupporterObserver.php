<?php

namespace App\Observers;

use App\Models\CampaignSupporter;

class CampaignSupporterObserver
{
    public function created(CampaignSupporter $row): void
    {
        $row->campaign->increment('supporter_count');
    }

    public function deleted(CampaignSupporter $row): void
    {
        Campaign::query()
            ->whereKey($row->campaign_id)
            ->where('supporter_count', '>', 0)
            ->decrement('supporter_count');
    }
}
