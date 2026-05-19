<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Ana sayfa «Canlı vitrin» — yalnızca veritabanındaki onaylı içerikler.
 */
final class HomeSpotlightFeed
{
    /**
     * @return array{
     *     videos: Collection<int, Post>,
     *     images: Collection<int, Post>,
     *     stories: Collection<int, Story>
     * }
     */
    public static function forRequest(Request $request): array
    {
        $ctx = PublicPostFeed::locationContext($request);
        $cityId = $ctx['cityId'];

        $postsQuery = Post::query()
            ->publicApproved()
            ->with(['user:id,name', 'category:id,name', 'city:id,name'])
            ->where(function ($q): void {
                $q->where(function ($u): void {
                    $u->whereNotNull('media_url')->where('media_url', '!=', '');
                })->orWhereNotNull('media');
            })
            ->orderByDesc('created_at')
            ->limit(120);

        if ($cityId !== null && (int) $cityId !== 0) {
            $postsQuery->where('city_id', (int) $cityId);
        }

        $videos = collect();
        $images = collect();

        foreach ($postsQuery->get() as $post) {
            $primary = PostMediaPresenter::primary($post);
            if ($primary === null) {
                continue;
            }

            if (($primary['type'] ?? '') === 'video') {
                if ($videos->count() < 4) {
                    $videos->push($post);
                }
            } elseif ($images->count() < 12) {
                $images->push($post);
            }

            if ($videos->count() >= 4 && $images->count() >= 12) {
                break;
            }
        }

        $storiesQuery = Story::query()
            ->active()
            ->whereNotNull('media_url')
            ->where('media_url', '!=', '')
            ->with(['user:id,name', 'city:id,name'])
            ->orderByDesc('created_at')
            ->limit(14);

        if ($cityId !== null && (int) $cityId !== 0) {
            $storiesQuery->where('city_id', (int) $cityId);
        }

        return [
            'videos' => $videos,
            'images' => $images,
            'stories' => $storiesQuery->get(),
        ];
    }
}
