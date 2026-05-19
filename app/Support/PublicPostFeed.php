<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\City;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class PublicPostFeed
{
    /**
     * @return array{cityId: int|null, lat: float|null, lng: float|null, relaxNearby: bool}
     */
    public static function locationContext(Request $request): array
    {
        $cookieCity = $request->cookie('simdibildir_city_id');

        $cityFromCookie = null;
        if (is_numeric($cookieCity)) {
            $cityFromCookie = (int) $cookieCity;
        }

        $cityId = $request->integer('city_id') ?: ($cityFromCookie ?: City::query()->where('plate', 34)->value('id'));

        $parsedLatLng = self::coordinatePair($request);

        $lat = $parsedLatLng['lat'];
        $lng = $parsedLatLng['lng'];

        $relaxNearby = false;
        if ($lat !== null && $lng !== null) {
            $flag = $request->query('relax_city');
            $relaxNearby = $flag === null ? true : filter_var($flag, FILTER_VALIDATE_BOOL);
        }

        return [
            'cityId' => $cityId !== null ? (int) $cityId : null,
            'lat' => $lat,
            'lng' => $lng,
            'relaxNearby' => $relaxNearby,
        ];
    }

    public static function paginate(Request $request, int $perPage = 15, ?int $districtId = null): LengthAwarePaginator
    {
        $today = Carbon::today()->format('Y-m-d');
        $ctx = self::locationContext($request);
        $cityId = $ctx['cityId'];
        $lat = $ctx['lat'];
        $lng = $ctx['lng'];
        $relaxNearby = $ctx['relaxNearby'];

        $postsQuery = Post::query()
            ->publicApproved()
            ->with([
                'user:id,name,verification_status',
                'category:id,name,slug',
                'city:id,name,slug',
                'district:id,name',
                'institution:id,name,verified',
                'institutions:id,name,verified',
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

        if ($districtId !== null && $districtId > 0) {
            $postsQuery->where('posts.district_id', $districtId);
        }

        if ($request->filled('q')) {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], (string) $request->query('q')).'%';
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

        $feed = (string) $request->query('feed', 'all');

        if ($feed === 'recent') {
            $postsQuery->orderByDesc('posts.created_at');
        } else {
            $postsQuery->orderByRaw('CASE WHEN DATE(posts.created_at) = ? THEN 1 ELSE 0 END DESC', [$today]);

            $driver = DB::connection()->getDriverName();
            if ($lat !== null && $lng !== null) {
                $postsQuery->orderByRaw(
                    '(CASE WHEN posts.latitude IS NOT NULL AND posts.longitude IS NOT NULL THEN 1 ELSE 0 END) DESC'
                );

                $postsQuery->when(
                    true,
                    fn ($q) => $driver === 'sqlite'
                        ? $q->orderByRaw(
                            '((CAST(posts.latitude AS REAL) - ?) * (CAST(posts.latitude AS REAL) - ?) + (CAST(posts.longitude AS REAL) - ?) * (CAST(posts.longitude AS REAL) - ?)) ASC',
                            [$lat, $lat, $lng, $lng]
                        )
                        : $q->orderByRaw(
                            '((posts.latitude - ?)*(posts.latitude - ?) + (posts.longitude - ?)*(posts.longitude - ?)) ASC',
                            [$lat, $lat, $lng, $lng]
                        )
                );
            }

            $postsQuery->orderByRaw('(posts.support_count * 10 + posts.comments_count * 6 + COALESCE(posts.follow_count, 0) * 8) DESC')
                ->orderByDesc('posts.created_at');
        }

        return $postsQuery->paginate(perPage: $perPage)->withQueryString();
    }

    /**
     * @return array{lat: float|null, lng: float|null}
     */
    public static function coordinatePair(Request $request): array
    {
        $pairs = [[$request->query('lat'), $request->query('lng')], [$request->cookie('simdibildir_lat'), $request->cookie('simdibildir_lng')]];
        foreach ($pairs as [$la, $lo]) {
            $lat = self::coordinate($la ?? null);
            $lng = self::coordinate($lo ?? null);

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
