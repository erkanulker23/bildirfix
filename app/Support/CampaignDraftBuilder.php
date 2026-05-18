<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class CampaignDraftBuilder
{
    public static function titleFromPurpose(string $purpose): string
    {
        $text = trim($purpose);
        $lower = mb_strtolower($text, 'UTF-8');

        foreach (['isterim ki ', 'istiyorum ki ', 'i̇sterim ki '] as $prefix) {
            if (str_starts_with($lower, $prefix)) {
                $text = trim(mb_substr($text, mb_strlen($prefix)));
                break;
            }
        }

        $text = rtrim($text, " .…");

        return Str::limit($text !== '' ? $text : __('Yeni kampanya'), 140, '…');
    }

    public static function excerptFromPurpose(string $purpose): string
    {
        return Str::limit(self::titleFromPurpose($purpose), 480, '…');
    }

    public static function description(string $purpose, ?string $personalStory = null): string
    {
        $parts = [trim($purpose)];

        if ($personalStory !== null && trim($personalStory) !== '') {
            $parts[] = "\n\n".__('Kişisel hikaye')."\n".trim($personalStory);
        }

        return implode('', $parts);
    }

    /**
     * @return array{title: string, excerpt: string, description: string, city_id: int|null}
     */
    public static function compose(array $data): array
    {
        $purpose = (string) ($data['purpose'] ?? '');
        $story = isset($data['personal_story']) ? (string) $data['personal_story'] : null;
        $scope = (string) ($data['scope'] ?? 'national');
        $cityId = isset($data['city_id']) ? (int) $data['city_id'] : null;

        $title = isset($data['title']) && trim((string) $data['title']) !== ''
            ? Str::limit(trim((string) $data['title']), 140, '…')
            : self::titleFromPurpose($purpose);

        return [
            'title' => $title,
            'excerpt' => self::excerptFromPurpose($purpose),
            'description' => self::description($purpose, $story),
            'city_id' => $scope === 'local' ? $cityId : null,
        ];
    }
}
