<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Str;

final readonly class SiteIntegrations
{
    public function __construct(
        public ?string $googleSiteVerification,
        public ?string $googleAnalyticsMeasurementId,
        public ?string $yandexVerification,
        public ?string $bingSiteVerification,
        public ?string $indexnowKey,
        public ?string $customHeadCss,
        public ?string $customHeadHtml,
        public ?string $customBodyHtml,
    ) {}

    public static function fromPlatform(): self
    {
        try {
            $platform = PlatformSetting::current();

            return new self(
                googleSiteVerification: self::nullIfBlank($platform->google_site_verification),
                googleAnalyticsMeasurementId: self::nullIfBlank($platform->google_analytics_measurement_id),
                yandexVerification: self::nullIfBlank($platform->yandex_verification),
                bingSiteVerification: self::nullIfBlank($platform->bing_site_verification),
                indexnowKey: self::nullIfBlank($platform->indexnow_key),
                customHeadCss: self::nullIfBlank($platform->custom_head_css),
                customHeadHtml: self::nullIfBlank($platform->custom_head_html),
                customBodyHtml: self::nullIfBlank($platform->custom_body_html),
            );
        } catch (\Throwable) {
            return self::empty();
        }
    }

    public static function empty(): self
    {
        return new self(null, null, null, null, null, null, null, null);
    }

    public function googleSiteVerificationEffective(): ?string
    {
        return $this->googleSiteVerification
            ?? self::nullIfBlank(config('seo.google_site_verification'));
    }

    public function yandexVerificationEffective(): ?string
    {
        return $this->yandexVerification
            ?? self::nullIfBlank(config('seo.yandex_verification'));
    }

    public function bingSiteVerificationEffective(): ?string
    {
        return $this->bingSiteVerification
            ?? self::nullIfBlank(config('seo.bing_site_verification'));
    }

    public function indexNowKeyFileUrl(): ?string
    {
        if ($this->indexnowKey === null) {
            return null;
        }

        return url('/'.$this->indexnowKey.'.txt');
    }

    public function indexNowConfigured(): bool
    {
        return $this->indexnowKey !== null;
    }

    public function googleAnalyticsConfigured(): bool
    {
        return $this->googleAnalyticsMeasurementId !== null
            && preg_match('/^G-[A-Z0-9]+$/i', $this->googleAnalyticsMeasurementId) === 1;
    }

    public static function generateIndexNowKey(): string
    {
        return Str::lower(Str::random(32));
    }

    private static function nullIfBlank(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
