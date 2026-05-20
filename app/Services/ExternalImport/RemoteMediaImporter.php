<?php

declare(strict_types=1);

namespace App\Services\ExternalImport;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class RemoteMediaImporter
{
    /**
     * @param  list<array{type?: string, url?: string}>  $remoteItems
     * @return list<array{type: string, url: string}>
     */
    public function importForPost(Post $post, array $remoteItems): array
    {
        $stored = [];

        foreach ($remoteItems as $item) {
            $url = isset($item['url']) ? trim((string) $item['url']) : '';
            if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $type = ($item['type'] ?? '') === 'video' ? 'video' : 'image';
            $path = $this->downloadToPublic($post->id, $url, $type);

            if ($path === null) {
                $stored[] = ['type' => $type, 'url' => $url];

                continue;
            }

            $stored[] = [
                'type' => $type,
                'url' => Storage::disk('public')->url($path),
            ];
        }

        return array_values($stored);
    }

    private function downloadToPublic(int $postId, string $url, string $type): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => (string) config('external_import.user_agent'),
            ])
                ->timeout((int) config('external_import.http_timeout', 25))
                ->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $body = $response->body();
        if (! is_string($body) || $body === '') {
            return null;
        }

        $ext = $this->guessExtension($url, $type);
        $dest = 'posts/'.$postId.'/imported/'.Str::uuid().'.'.$ext;

        Storage::disk('public')->put($dest, $body);

        return $dest;
    }

    private function guessExtension(string $url, string $type): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = is_string($path) ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';

        if ($ext !== '' && strlen($ext) <= 5) {
            return $ext;
        }

        return $type === 'video' ? 'mp4' : 'jpg';
    }
}
