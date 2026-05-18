<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PublicStoryFeed
{
    /**
     * Ana sayfa / akış ile aynı konum mantığında aktif hikâyeler.
     *
     * @return Collection<int, Story>
     */
    public static function forRequest(Request $request, int $limit = 42): Collection
    {
        $ctx = PublicPostFeed::locationContext($request);
        $cityId = $ctx['cityId'];
        $lat = $ctx['lat'];
        $lng = $ctx['lng'];
        $relaxNearby = $ctx['relaxNearby'];

        $driver = DB::connection()->getDriverName();

        $storiesQuery = Story::query()
            ->active()
            ->with(['user:id,name,role', 'city:id,name']);

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

        return $storiesQuery->latest()->take($limit)->get();
    }

    /**
     * İl / ilçe sayfası: şehre bağlı aktif hikâyeler.
     *
     * @return Collection<int, Story>
     */
    public static function forCityId(int $cityId, int $limit = 48): Collection
    {
        if ($cityId <= 0) {
            return collect();
        }

        return Story::query()
            ->active()
            ->where('city_id', $cityId)
            ->with(['user:id,name,role', 'city:id,name'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * @param  Collection<int, Story>  $stories
     * @return list<array<string, mixed>>
     */
    public static function viewerPayload(Collection $stories): array
    {
        return $stories->map(static function (Story $s): array {
            $url = trim((string) ($s->media_url ?? ''));
            $mediaType = 'image';
            if ($url !== '') {
                $lower = strtolower($url);
                if (
                    preg_match('#\.(mp4|webm|ogg)(\?|$)#i', $lower)
                    || str_contains($lower, 'youtube.com')
                    || str_contains($lower, 'youtu.be')
                    || str_contains($lower, 'vimeo.com')
                ) {
                    $mediaType = 'video';
                }
            }

            $loc = null;
            if ($s->relationLoaded('city') && $s->city) {
                $loc = $s->city->name;
            }

            return [
                'id' => $s->id,
                'media_url' => $url,
                'media_type' => $mediaType,
                'description' => (string) ($s->description ?? ''),
                'user' => ['name' => $s->user?->name ?? '?'],
                'created_at_human' => $s->created_at?->diffForHumans() ?? '',
                'location_text' => $loc,
            ];
        })->values()->all();
    }
}
