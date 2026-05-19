<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignTopic;
use Illuminate\View\View;

class CampaignIndexController extends Controller
{
    public function __invoke(): View
    {
        $cityId = request()->filled('city_id') ? request()->integer('city_id') : null;
        $topicId = request()->filled('konu') ? request()->integer('konu') : null;

        $q = Campaign::query()
            ->publicApproved()
            ->with(['user:id,name', 'city:id,name', 'topic:id,name,slug'])
            ->orderByDesc('supporter_count')
            ->orderByDesc('created_at');

        if ($cityId !== null && $cityId !== 0) {
            $q->where(function ($w) use ($cityId): void {
                $w->whereNull('city_id')->orWhere('city_id', $cityId);
            });
        }

        if ($topicId !== null && $topicId !== 0) {
            $q->where('campaign_topic_id', $topicId);
        }

        $campaigns = $q->paginate(15)->withQueryString();

        $cities = \App\Models\City::query()->orderBy('name')->get(['id', 'name', 'plate']);
        $topics = CampaignTopic::query()
            ->whereHas('campaigns', fn ($q) => $q->publicApproved())
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'group_key']);

        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'cities' => $cities,
            'topics' => $topics,
            'topicGroups' => config('campaign_topics.groups', []),
            'activeCityFilter' => $cityId ?: null,
            'activeTopicFilter' => $topicId ?: null,
            'hidePageHero' => true,
            'seo' => [
                'description' => (string) config('seo.default_meta_description'),
                'canonical' => route('campaigns.index', [], true),
                'og_title' => config('app.name').' • '.__('Toplumsal kampanyalar'),
                'og_type' => 'website',
            ],
            'structuredData' => [
                \App\Support\Seo::webSiteStructuredData(),
            ],
        ]);
    }
}
