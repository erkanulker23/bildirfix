<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\View\View;

class CampaignIndexController extends Controller
{
    public function __invoke(): View
    {
        $cityId = request()->filled('city_id') ? request()->integer('city_id') : null;

        $q = Campaign::query()
            ->publicApproved()
            ->with(['user:id,name', 'city:id,name'])
            ->orderByDesc('supporter_count')
            ->orderByDesc('created_at');

        if ($cityId !== null && $cityId !== 0) {
            $q->where(function ($w) use ($cityId): void {
                $w->whereNull('city_id')->orWhere('city_id', $cityId);
            });
        }

        $campaigns = $q->paginate(15)->withQueryString();

        $cities = \App\Models\City::query()->orderBy('name')->get(['id', 'name', 'plate']);

        return view('campaigns.index', [
            'campaigns' => $campaigns,
            'cities' => $cities,
            'activeCityFilter' => $cityId ?: null,
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
