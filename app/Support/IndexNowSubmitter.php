<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class IndexNowSubmitter
{
    private const ENDPOINT = 'https://api.indexnow.org/indexnow';

    /**
     * @param  list<string>  $absoluteUrls
     */
    public function submitUrls(array $absoluteUrls): bool
    {
        $integrations = SiteIntegrations::fromPlatform();
        $key = $integrations->indexnowKey;

        if ($key === null) {
            return false;
        }

        $urls = array_values(array_filter(array_map(
            static fn (mixed $u): ?string => is_string($u) && $u !== '' ? $u : null,
            $absoluteUrls,
        )));

        if ($urls === []) {
            return false;
        }

        $host = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return false;
        }

        $keyLocation = $integrations->indexNowKeyFileUrl();
        if ($keyLocation === null) {
            return false;
        }

        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->post(self::ENDPOINT, [
                    'host' => $host,
                    'key' => $key,
                    'keyLocation' => $keyLocation,
                    'urlList' => array_slice($urls, 0, 100),
                ]);

            return $response->successful() || $response->status() === 202;
        } catch (\Throwable $e) {
            Log::warning('IndexNow submit failed', ['message' => $e->getMessage()]);

            return false;
        }
    }

    public function submitUrl(string $absoluteUrl): bool
    {
        return $this->submitUrls([$absoluteUrl]);
    }
}
