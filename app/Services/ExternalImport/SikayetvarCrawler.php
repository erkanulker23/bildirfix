<?php

declare(strict_types=1);

namespace App\Services\ExternalImport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Şikayetvar marka/kurum sayfalarından şikâyet listesi ve detay içeriği okur.
 */
final class SikayetvarCrawler
{
    private const SOURCE = 'sikayetvar';

    /**
     * @return list<array{
     *   external_id: string,
     *   slug_path: string,
     *   title: string,
     *   author_name: string|null,
     *   description: string|null,
     *   published_at: string|null
     * }>
     */
    public function fetchListing(string $brandSlug, int $maxPages = 50): array
    {
        $all = [];
        $seen = [];

        for ($page = 1; $page <= max(1, $maxPages); $page++) {
            $url = $page === 1
                ? 'https://www.sikayetvar.com/'.$brandSlug
                : 'https://www.sikayetvar.com/'.$brandSlug.'?page='.$page;

            $html = $this->fetchHtml($url);
            $batch = $this->parseListingCards($html, $brandSlug);

            if ($batch === []) {
                break;
            }

            foreach ($batch as $row) {
                if (isset($seen[$row['external_id']])) {
                    continue;
                }
                $seen[$row['external_id']] = true;
                $all[] = $row;
            }

            if (count($batch) < 5) {
                break;
            }
        }

        return $all;
    }

    /**
     * @return array{
     *   external_id: string,
     *   title: string,
     *   author_name: string|null,
     *   description: string,
     *   published_at: string|null,
     *   media: list<array{type: string, url: string}>
     * }
     */
    public function fetchDetail(string $slugPath): array
    {
        $url = str_starts_with($slugPath, 'http')
            ? $slugPath
            : 'https://www.sikayetvar.com/'.ltrim($slugPath, '/');

        $html = $this->fetchHtml($url);

        if (! preg_match('/data-id="(\d+)"/', $html, $idMatch)) {
            throw new RuntimeException(__('Şikayetvar detay sayfasında şikâyet kimliği bulunamadı.'));
        }

        $title = $this->matchFirst($html, '/<h1[^>]*class="[^"]*complaint-title[^"]*"[^>]*>(.*?)<\/h1>/s')
            ?? $this->matchFirst($html, '/<meta property="og:title" content="([^"]+)"/')
            ?? __('İçe aktarılan şikâyet');

        $title = $this->cleanText(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        $author = $this->matchFirst($html, '/class="username"[^>]*aria-label="([^"]+)"/')
            ?? $this->matchFirst($html, '/"author":\{"@type":"Person","name":"([^"]+)"/');

        $description = $this->extractDescription($html);
        $publishedAt = $this->matchFirst($html, '/"datePublished":"([^"]+)"/')
            ?? $this->matchFirst($html, '/<time[^>]*datetime="([^"]+)"/');

        return [
            'external_id' => $idMatch[1],
            'title' => Str::limit($title, 250, ''),
            'author_name' => $author !== null ? $this->cleanText($author) : null,
            'description' => $description,
            'published_at' => $publishedAt,
            'media' => $this->extractMediaUrls($html),
        ];
    }

    public static function sourceKey(): string
    {
        return self::SOURCE;
    }

    /**
     * @return list<array{external_id: string, slug_path: string, title: string, author_name: string|null, description: string|null, published_at: string|null}>
     */
    private function parseListingCards(string $html, string $brandSlug): array
    {
        $out = [];

        if (! preg_match_all(
            '/<article[^>]*class="[^"]*card-v2[^"]*"[^>]*data-id="(\d+)"[^>]*>(.*?)<\/article>/s',
            $html,
            $articles,
            PREG_SET_ORDER,
        )) {
            return $out;
        }

        foreach ($articles as $article) {
            $block = $article[0];
            $externalId = $article[1];

            if (! preg_match('/<a href="(\/[^"]+)"[^>]*title="([^"]*)"/s', $block, $link)) {
                continue;
            }

            $path = $link[1];
            if (! str_starts_with($path, '/'.$brandSlug.'/')) {
                continue;
            }

            $title = $this->cleanText(html_entity_decode($link[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $author = null;
            if (preg_match('/class="username"[^>]*aria-label="([^"]+)"/', $block, $authorMatch)) {
                $author = $this->cleanText($authorMatch[1]);
            }

            $snippet = null;
            if (preg_match('/<p class="complaint-description[^"]*">(.*?)<\/p>/s', $block, $descMatch)) {
                $snippet = $this->cleanText(html_entity_decode(strip_tags($descMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            $publishedAt = null;
            if (preg_match('/<time[^>]*datetime="([^"]+)"/', $block, $timeMatch)) {
                $publishedAt = $timeMatch[1];
            }

            $out[] = [
                'external_id' => $externalId,
                'slug_path' => ltrim($path, '/'),
                'title' => Str::limit($title, 250, ''),
                'author_name' => $author,
                'description' => $snippet,
                'published_at' => $publishedAt,
            ];
        }

        return $out;
    }

    /**
     * @return list<array{type: string, url: string}>
     */
    private function extractMediaUrls(string $html): array
    {
        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        preg_match_all('#https://files\.sikayetvar\.com/complaint/[a-zA-Z0-9/_\-.]+#', $decoded, $matches);

        $items = [];
        $seen = [];

        foreach ($matches[0] as $rawUrl) {
            $url = rtrim($rawUrl, '"\'');

            if (preg_match('/_\d+x\d+\./', $url) || str_contains($url, '_preview.mp4')) {
                continue;
            }

            $lower = strtolower($url);
            $type = str_ends_with($lower, '.mp4') || str_contains($lower, '.mp4?') ? 'video' : 'image';

            if ($type === 'image' && ! preg_match('/\.(jpe?g|png|webp|gif)(\?|$)/i', $lower)) {
                continue;
            }

            if (isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;

            $items[] = ['type' => $type, 'url' => $url];
        }

        return array_values($items);
    }

    private function extractDescription(string $html): string
    {
        if (preg_match('/"reviewBody":"((?:\\\\.|[^"\\\\])*)"/', $html, $jsonBody)) {
            $body = stripcslashes($jsonBody[1]);

            return Str::limit($this->cleanText($body), 15000, '');
        }

        if (preg_match('/<div class="complaint-detail-description[^"]*">(.*?)<\/div>/s', $html, $block)) {
            return Str::limit($this->cleanText(html_entity_decode(strip_tags($block[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 15000, '');
        }

        return '';
    }

    private function fetchHtml(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => (string) config('external_import.user_agent'),
            'Accept-Language' => 'tr-TR,tr;q=0.9',
        ])
            ->timeout((int) config('external_import.http_timeout', 25))
            ->get($url);

        if (! $response->successful()) {
            throw new RuntimeException(__('Şikayetvar sayfası alınamadı (:status).', ['status' => $response->status()]));
        }

        $body = $response->body();

        return is_string($body) ? $body : '';
    }

    private function matchFirst(string $html, string $pattern): ?string
    {
        if (! preg_match($pattern, $html, $m)) {
            return null;
        }

        return trim($m[1]);
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
