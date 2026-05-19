<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Post;
use App\Support\PageHero;
use App\Support\PublicStoryFeed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class CityPublicController extends Controller
{
    public function __invoke(City $city): View
    {
        $postsQuery = Post::query()
            ->publicApproved()
            ->where('posts.city_id', $city->id)
            ->with([
                'user:id,name,verification_status',
                'category:id,name,slug',
                'city:id,name',
                'district:id,name',
                'institution:id,name,verified',
                'institutions:id,name,verified',
            ]);

        if (Auth::check()) {
            $uid = Auth::id();
            $postsQuery
                ->withExists(['supports as viewer_supported' => fn ($q) => $q->where('user_id', $uid)])
                ->withExists(['follows as viewer_following' => fn ($q) => $q->where('user_id', $uid)]);
        }

        $posts = $postsQuery
            ->orderByDesc('posts.created_at')
            ->paginate(perPage: 15)
            ->withQueryString();

        $cityStories = PublicStoryFeed::forCityId((int) $city->id, 48);
        $storiesViewerPayload = PublicStoryFeed::viewerPayload($cityStories);

        return view('cities.show', [
            'city' => $city,
            'pageHero' => PageHero::make(
                __('Şehir sayfası'),
                $city->name,
                null,
                __(':city ili için onaylı ve yayında olan bildirimler.', ['city' => $city->name]),
            ),
            'posts' => $posts,
            'cityStories' => $cityStories,
            'storiesViewerPayload' => $storiesViewerPayload,
            'seo' => [
                'description' => Str::limit(
                    __(':city ili için onaylı ve yayında olan şikâyet kayıtları.', ['city' => $city->name]),
                    320
                ),
                'canonical' => route('cities.show', $city, absolute: true),
                'og_title' => $city->name.' • '.config('app.name'),
                'og_type' => 'website',
            ],
            'structuredData' => [],
        ]);
    }
}
