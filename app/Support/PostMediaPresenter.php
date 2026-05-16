<?php

namespace App\Support;

use App\Models\Post;

/**
 * Şikâyet kartında gösterilecek tek bir ön plan medyası (resim URL veya video URL).
 */
final class PostMediaPresenter
{
    /** @var array{type: string, url: string, poster?: string|null}|null */
    public static function primary(?Post $post): ?array
    {
        if (! $post) {
            return null;
        }

        $mediaArr = $post->media ?? [];
        if (is_array($mediaArr)) {
            foreach ($mediaArr as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $url = isset($item['url']) ? (string) $item['url'] : '';
                if ($url === '') {
                    continue;
                }
                $type = isset($item['type']) ? (string) $item['type'] : '';
                $guessed = self::guessTypeFromUrl($url, $type);

                return ['type' => $guessed['type'], 'url' => $url, 'poster' => $guessed['poster'] ?? null];
            }
        }

        $single = isset($post->media_url) ? trim((string) $post->media_url) : '';
        if ($single !== '') {
            $guessed = self::guessTypeFromUrl($single, '');

            return ['type' => $guessed['type'], 'url' => $single, 'poster' => $guessed['poster'] ?? null];
        }

        return null;
    }

    /**
     * Kart / detay galerisinde kullanılmak üzere tüm medya öğeleri.
     *
     * @return list<array{type: string, url: string, poster?: string|null}>
     */
    public static function all(?Post $post): array
    {
        if (! $post) {
            return [];
        }

        $out = [];
        $mediaArr = $post->media ?? [];
        if (is_array($mediaArr)) {
            foreach ($mediaArr as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $url = isset($item['url']) ? (string) $item['url'] : '';
                if ($url === '') {
                    continue;
                }
                $type = isset($item['type']) ? (string) $item['type'] : '';
                $guessed = self::guessTypeFromUrl($url, $type);
                $row = ['type' => $guessed['type'], 'url' => $url];
                if (array_key_exists('poster', $guessed)) {
                    $row['poster'] = $guessed['poster'];
                }
                $out[] = $row;
            }
        }

        if ($out === []) {
            $single = isset($post->media_url) ? trim((string) $post->media_url) : '';
            if ($single !== '') {
                $guessed = self::guessTypeFromUrl($single, '');

                $row = ['type' => $guessed['type'], 'url' => $single];
                if (array_key_exists('poster', $guessed)) {
                    $row['poster'] = $guessed['poster'];
                }
                $out[] = $row;
            }
        }

        return $out;
    }

    /**
     * @return array{type: string, poster?: ?string}
     */
    protected static function guessTypeFromUrl(string $url, string $declaredType): array
    {
        $t = strtolower($declaredType);
        if ($t === 'video' || $t === 'image') {
            return ['type' => $t];
        }

        $lower = strtolower($url);
        if (
            preg_match('#\.(mp4|webm|ogg)(\?|$)#i', $lower)
            || str_contains($lower, 'youtube.com')
            || str_contains($lower, 'youtu.be')
            || str_contains($lower, 'vimeo.com')
        ) {
            $poster = null;
            if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([\w\-]+)#i', $url, $m)) {
                $poster = 'https://img.youtube.com/vi/'.$m[1].'/hqdefault.jpg';
            }

            return ['type' => 'video', 'poster' => $poster];
        }

        return ['type' => 'image'];
    }

    /** @deprecated use primary(); kept tests */
    public static function thumbnailUrl(?Post $post): ?string
    {
        $p = self::primary($post);

        if (! $p) {
            return null;
        }

        if (($p['type'] ?? '') === 'video' && ! empty($p['poster'])) {
            return $p['poster'];
        }

        return $p['url'] ?? null;
    }
}
