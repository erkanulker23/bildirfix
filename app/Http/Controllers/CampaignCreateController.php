<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CampaignTopic;
use Illuminate\View\View;

class CampaignCreateController extends Controller
{
    public function __invoke(): View
    {
        return view('campaigns.create', [
            'campaignTopics' => CampaignTopic::query()->orderBy('sort_order')->get(['id', 'name', 'group_key']),
            'topicGroups' => config('campaign_topics.groups', []),
        ]);
    }
}
