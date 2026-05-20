<?php

declare(strict_types=1);

namespace App\Services\ExternalImport;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Models\ExternalImportSource;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class ExternalComplaintImportService
{
    public function __construct(
        private readonly SikayetvarCrawler $crawler,
        private readonly ImportUserResolver $userResolver,
        private readonly RemoteMediaImporter $mediaImporter,
    ) {}

    /**
     * @return array{imported: int, skipped: int, errors: list<string>}
     */
    public function run(ExternalImportSource $source): array
    {
        $slug = $this->resolveBrandSlug($source);
        $source->forceFill(['source_slug' => $slug])->save();

        $listing = $this->crawler->fetchListing($slug, (int) $source->max_pages);
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $detailBudget = (int) config('external_import.max_detail_fetches_per_run', 80);
        $detailsUsed = 0;

        foreach ($listing as $row) {
            $externalId = $row['external_id'];

            $exists = Post::query()
                ->where('external_source', SikayetvarCrawler::sourceKey())
                ->where('external_id', $externalId)
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            try {
                $payload = $row;
                if ($source->fetch_media && $detailsUsed < $detailBudget) {
                    $detail = $this->crawler->fetchDetail($row['slug_path']);
                    $detailsUsed++;
                    $payload = array_merge($row, $detail);
                }

                $this->createPostFromPayload($source, $payload);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = '#'.$externalId.': '.$e->getMessage();
            }
        }

        $source->forceFill([
            'last_synced_at' => now(),
            'last_imported_count' => $imported,
            'last_sync_error' => $errors === [] ? null : Str::limit(implode("\n", $errors), 5000, ''),
        ])->save();

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createPostFromPayload(ExternalImportSource $source, array $payload): Post
    {
        $authorName = isset($payload['author_name']) ? (string) $payload['author_name'] : null;
        $user = $this->userResolver->resolve(SikayetvarCrawler::sourceKey(), $authorName);

        $slugPath = (string) ($payload['slug_path'] ?? '');
        $sourceUrl = 'https://www.sikayetvar.com/'.ltrim($slugPath, '/');

        $description = trim((string) ($payload['description'] ?? ''));
        if ($description === '') {
            $description = null;
        }

        $createdAt = null;
        if (! empty($payload['published_at'])) {
            try {
                $createdAt = Carbon::parse((string) $payload['published_at']);
            } catch (\Throwable) {
                $createdAt = null;
            }
        }

        $moderation = $this->defaultModeration($source);

        $post = Post::query()->create([
            'user_id' => $user->id,
            'title' => Str::limit((string) ($payload['title'] ?? __('İçe aktarılan şikâyet')), 250, ''),
            'description' => $description,
            'media_url' => null,
            'media' => [],
            'type' => 'complaint',
            'external_source' => SikayetvarCrawler::sourceKey(),
            'external_id' => (string) ($payload['external_id'] ?? ''),
            'source_url' => $sourceUrl,
            'external_import_source_id' => $source->id,
            'imported_at' => now(),
            'city_id' => $source->institution?->city_id,
            'district_id' => null,
            'neighborhood_id' => null,
            'latitude' => null,
            'longitude' => null,
            'category_id' => null,
            'institution_id' => $source->institution_id,
            'status' => PostStatus::Open,
            'moderation_status' => $moderation,
            'moderated_at' => $moderation === PostModerationStatus::Approved ? now() : null,
            'moderated_by_user_id' => null,
            'moderation_note' => $moderation === PostModerationStatus::Approved
                ? __('Şikayetvar içe aktarımı — otomatik onay')
                : __('Şikayetvar içe aktarımı — moderasyon bekliyor'),
        ]);

        if ($createdAt !== null) {
            $post->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();
        }

        $institutionIds = [];
        if ($source->institution_id !== null) {
            $institutionIds[] = (int) $source->institution_id;
        }
        if ($institutionIds !== []) {
            $post->syncTargetInstitutions($institutionIds);
        }

        /** @var list<array{type?: string, url?: string}> $remoteMedia */
        $remoteMedia = is_array($payload['media'] ?? null) ? $payload['media'] : [];
        if ($remoteMedia !== []) {
            $mediaItems = $this->mediaImporter->importForPost($post, $remoteMedia);
            if ($mediaItems !== []) {
                $first = $mediaItems[0];
                $post->forceFill([
                    'media' => $mediaItems,
                    'media_url' => $first['url'] ?? null,
                ])->save();
            }
        }

        return $post;
    }

    private function defaultModeration(ExternalImportSource $source): PostModerationStatus
    {
        $value = strtolower(trim((string) $source->default_moderation));

        return match ($value) {
            'approved' => PostModerationStatus::Approved,
            'rejected' => PostModerationStatus::Rejected,
            default => PostModerationStatus::Pending,
        };
    }

    private function resolveBrandSlug(ExternalImportSource $source): string
    {
        if (is_string($source->source_slug) && trim($source->source_slug) !== '') {
            return trim($source->source_slug);
        }

        $url = trim($source->source_url);
        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '' || $path === '/') {
            throw new \InvalidArgumentException(__('Geçerli bir Şikayetvar marka URL’si girin.'));
        }

        return trim($path, '/');
    }
}
