<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\City;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $today = Carbon::today()->format('Y-m-d');
        $cookieCity = request()->cookie('bildir_city_id');

        $cityFromCookie = null;
        if (is_numeric($cookieCity)) {
            $cityFromCookie = (int) $cookieCity;
        }

        $cityId = request()->integer('city_id') ?: ($cityFromCookie ?: City::query()->where('plate', 34)->value('id'));

        $parsedLatLng = static::coordinatePair();

        $lat = $parsedLatLng['lat'];
        $lng = $parsedLatLng['lng'];

        $relaxNearby = false;
        if ($lat !== null && $lng !== null) {
            $flag = request('relax_city');
            $relaxNearby = $flag === null ? true : filter_var($flag, FILTER_VALIDATE_BOOL);
        }

        $postsQuery = Post::query()
            ->publicApproved()
            ->with([
                'user:id,name',
                'category:id,name,slug',
                'city:id,name',
                'district:id,name',
                'institution:id,name,verified',
            ]);

        if ($relaxNearby && $lat !== null && $lng !== null) {
            $delta = 0.52;
            $minLat = $lat - $delta;
            $maxLat = $lat + $delta;
            $minLng = $lng - $delta;
            $maxLng = $lng + $delta;

            $postsQuery->where(function ($q) use ($cityId, $minLat, $maxLat, $minLng, $maxLng): void {
                $q->where(function ($gq) use ($minLat, $maxLat, $minLng, $maxLng): void {
                    $gq->whereNotNull('posts.latitude')
                        ->whereNotNull('posts.longitude')
                        ->whereBetween('posts.latitude', [$minLat, $maxLat])
                        ->whereBetween('posts.longitude', [$minLng, $maxLng]);
                });

                if ($cityId !== null && (int) $cityId !== 0) {
                    $q->orWhere(function ($cq) use ($cityId): void {
                        $cq->where('posts.city_id', (int) $cityId)
                            ->where(function ($lq): void {
                                $lq->whereNull('posts.latitude')->orWhereNull('posts.longitude');
                            });
                    });
                }
            });
        } elseif ($cityId !== null && (int) $cityId !== 0) {
            $postsQuery->where('posts.city_id', $cityId);
        }

        if (request()->filled('category_id')) {
            $postsQuery->where('category_id', request()->integer('category_id'));
        }

        if (request()->filled('q')) {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], (string) request('q')).'%';
            $postsQuery->where(function ($query) use ($term): void {
                $query->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if (Auth::check()) {
            $uid = Auth::id();
            $postsQuery
                ->withExists(['supports as viewer_supported' => fn ($q) => $q->where('user_id', $uid)])
                ->withExists(['follows as viewer_following' => fn ($q) => $q->where('user_id', $uid)]);
        }

        $postsQuery->orderByRaw('CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END DESC', [$today]);

        $driver = DB::connection()->getDriverName();
        if ($lat !== null && $lng !== null) {
            $postsQuery->orderByRaw(
                '(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 ELSE 0 END) DESC'
            );

            $postsQuery->when(
                true,
                fn ($q) => $driver === 'sqlite'
                    ? $q->orderByRaw(
                        '((CAST(latitude AS REAL) - ?) * (CAST(latitude AS REAL) - ?) + (CAST(longitude AS REAL) - ?) * (CAST(longitude AS REAL) - ?)) ASC',
                        [$lat, $lat, $lng, $lng]
                    )
                    : $q->orderByRaw(
                        '((latitude - ?)*(latitude - ?) + (longitude - ?)*(longitude - ?)) ASC',
                        [$lat, $lat, $lng, $lng]
                    )
            );
        }

        $postsQuery->orderByRaw('(support_count * 10 + comments_count * 6 + COALESCE(follow_count, 0) * 8) DESC')
            ->orderByDesc('created_at');

        $posts = $postsQuery->paginate(perPage: 15)->withQueryString();

        $storiesQuery = Story::query()
            ->active()
            ->with(['user:id,name', 'city:id,name']);

        if ($relaxNearby && $lat !== null && $lng !== null) {
            $delta = 0.52;
            $minLat = $lat - $delta;
            $maxLat = $lat + $delta;
            $minLng = $lng - $delta;
            $maxLng = $lng + $delta;

            $storiesQuery->where(function ($q) use ($cityId, $minLat, $maxLat, $minLng, $maxLng): void {
                $q->where(function ($gq) use ($minLat, $maxLat, $minLng, $maxLng): void {
                    $gq->whereNotNull('stories.latitude')
                        ->whereNotNull('stories.longitude')
                        ->whereBetween('stories.latitude', [$minLat, $maxLat])
                        ->whereBetween('stories.longitude', [$minLng, $maxLng]);
                });

                if ($cityId !== null && (int) $cityId !== 0) {
                    $q->orWhere(function ($cq) use ($cityId): void {
                        $cq->where('stories.city_id', (int) $cityId)
                            ->where(function ($lq): void {
                                $lq->whereNull('stories.latitude')->orWhereNull('stories.longitude');
                            });
                    });
                }
            });
        } elseif ($cityId !== null && (int) $cityId !== 0) {
            $storiesQuery->where('city_id', $cityId);
        }

        if ($lat !== null && $lng !== null) {
            $storiesQuery->orderByRaw(
                '(CASE WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN 1 ELSE 0 END) DESC'
            );
            $storiesQuery->when(
                true,
                fn ($q) => $driver === 'sqlite'
                    ? $q->orderByRaw(
                        '((CAST(latitude AS REAL) - ?) * (CAST(latitude AS REAL) - ?) + (CAST(longitude AS REAL) - ?) * (CAST(longitude AS REAL) - ?)) ASC',
                        [$lat, $lat, $lng, $lng]
                    )
                    : $q->orderByRaw(
                        '((latitude - ?)*(latitude - ?) + (longitude - ?)*(longitude - ?)) ASC',
                        [$lat, $lat, $lng, $lng]
                    )
            );
        }

        $stories = $storiesQuery->latest()->take(42)->get();
        $cities = City::query()->orderBy('name')->get(['id', 'name', 'plate']);
        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);

        $published = Post::query()->publicApproved();
        $publishedTotal = (clone $published)->count();
        $withEvidenceCount = (clone $published)->where(function ($q): void {
            $q->where(function ($u): void {
                $u->whereNotNull('media_url')->where('media_url', '!=', '');
            })->orWhere(function ($u): void {
                $u->whereNotNull('latitude')->whereNotNull('longitude');
            });
        })->count();

        $evidencePct = $publishedTotal > 0 ? min(100, (int) round(100 * $withEvidenceCount / $publishedTotal)) : 0;

        $platformStats = [
            'members' => User::query()->whereIn('role', [UserRole::User, UserRole::VerifiedUser])->count(),
            'brands' => Institution::query()->count(),
            'resolved' => max(0, (clone $published)->where('status', PostStatus::Resolved)->count()),
            'evidence_pct' => $evidencePct,
            'last30' => max(0, (clone $published)->where('created_at', '>=', now()->subDays(30))->count()),
            'published' => max(0, $publishedTotal),
            'campaigns_live' => max(0, Campaign::query()->publicApproved()->count()),
        ];

        $featuredCampaignQuery = Campaign::query()
            ->publicApproved()
            ->with(['city:id,name']);

        if ($cityId !== null && (int) $cityId !== 0) {
            $featuredCampaignQuery->where(function ($w) use ($cityId): void {
                $w->whereNull('city_id')->orWhere('city_id', (int) $cityId);
            });
        }

        $featuredCampaigns = $featuredCampaignQuery->orderByDesc('supporter_count')->take(10)->get();

        return view('home', [
            'posts' => $posts,
            'stories' => $stories,
            'cities' => $cities,
            'categories' => $categories,
            'featuredCampaigns' => $featuredCampaigns,
            'platformStats' => $platformStats,
            'activeCityId' => $cityId,
            'searchQuery' => (string) request('q', ''),
            'nearLat' => $lat,
            'nearLng' => $lng,
            'geoActive' => $lat !== null && $lng !== null,
            'relaxNearby' => $relaxNearby,
            'seo' => [
                'description' => (string) config('seo.default_meta_description'),
                'canonical' => request()->fullUrl(),
                'og_title' => config('app.name').' • '.__('Son şikâyetler'),
                'og_type' => 'website',
            ],
            'structuredData' => [
                \App\Support\Seo::webSiteStructuredData(),
            ],
        ]);
    }

    /**
     * @return array{lat: float|null, lng: float|null}
     */
    protected static function coordinatePair(): array
    {
        $pairs = [[request('lat'), request('lng')], [request()->cookie('bildir_lat'), request()->cookie('bildir_lng')]];
        foreach ($pairs as [$la, $lo]) {
            $lat = static::coordinate($la ?? null);
            $lng = static::coordinate($lo ?? null);

            if ($lat !== null && $lng !== null) {
                return ['lat' => $lat, 'lng' => $lng];
            }
        }

        return ['lat' => null, 'lng' => null];
    }

    protected static function coordinate(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $f = filter_var($value, FILTER_VALIDATE_FLOAT);

        return $f === false ? null : round((float) $f, 6);
    }
}
