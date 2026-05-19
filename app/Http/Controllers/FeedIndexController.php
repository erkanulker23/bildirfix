<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Support\PublicPostFeed;
use App\Support\PublicStoryFeed;
use App\Support\Seo;
use Illuminate\View\View;

final class FeedIndexController extends Controller
{
    public function __invoke(): View
    {
        $ctx = PublicPostFeed::locationContext(request());
        $cityId = $ctx['cityId'];
        $lat = $ctx['lat'];
        $lng = $ctx['lng'];
        $relaxNearby = $ctx['relaxNearby'];

        $activeDistrictId = null;
        if (request()->filled('district_id') && $cityId !== null) {
            $activeDistrictId = District::query()
                ->where('city_id', $cityId)
                ->whereKey(request()->integer('district_id'))
                ->value('id');
        }

        $posts = PublicPostFeed::paginate(request(), 15, $activeDistrictId ? (int) $activeDistrictId : null);
        $cities = City::query()->orderBy('name')->get(['id', 'name', 'plate']);
        $districts = $cityId !== null
            ? District::query()->where('city_id', $cityId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $feedStories = PublicStoryFeed::forRequest(request(), 48);
        $storiesViewerPayload = PublicStoryFeed::viewerPayload($feedStories);

        return view('feed.index', [
            'posts' => $posts,
            'cities' => $cities,
            'districts' => $districts,
            'activeCityId' => $cityId,
            'activeDistrictId' => $activeDistrictId,
            'searchQuery' => (string) request('q', ''),
            'nearLat' => $lat,
            'nearLng' => $lng,
            'geoActive' => $lat !== null && $lng !== null,
            'relaxNearby' => $relaxNearby,
            'feedStories' => $feedStories,
            'storiesViewerPayload' => $storiesViewerPayload,
            'seo' => [
                'description' => (string) config('seo.default_meta_description'),
                'canonical' => request()->fullUrl(),
                'og_title' => config('app.name').' • '.__('Akış'),
                'og_type' => 'website',
            ],
            'structuredData' => [
                Seo::webSiteStructuredData(),
            ],
        ]);
    }
}
