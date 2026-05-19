<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PlatformSetting;

final readonly class SiteBranding
{
    public function __construct(
        public ?string $homepageSeoTitle,
        public ?string $homepageSeoDescription,
        public ?string $siteLogoPath,
        public ?string $faviconPath,
        public ?string $homepageOgImagePath,
    ) {}

    public static function fromPlatform(): self
    {
        try {
            $platform = PlatformSetting::current();

            return new self(
                homepageSeoTitle: self::nullIfBlank($platform->homepage_seo_title),
                homepageSeoDescription: self::nullIfBlank($platform->homepage_seo_description),
                siteLogoPath: self::nullIfBlank($platform->site_logo_path),
                faviconPath: self::nullIfBlank($platform->favicon_path),
                homepageOgImagePath: self::nullIfBlank($platform->homepage_og_image_path),
            );
        } catch (\Throwable) {
            return self::empty();
        }
    }

    public static function empty(): self
    {
        return new self(null, null, null, null, null);
    }

    public static function defaultHomepageTitle(): string
    {
        $fromConfig = config('branding.default_homepage_title');
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return config('app.name').' • '.__('Kent sorun bildir ve çözüm ağı');
    }

    public static function defaultHomepageDescription(): string
    {
        $fromConfig = config('branding.default_homepage_description');
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return (string) config('seo.default_meta_description');
    }

    public function homepageTitle(): string
    {
        return $this->homepageSeoTitle ?? self::defaultHomepageTitle();
    }

    public function homepageDescription(): string
    {
        return $this->homepageSeoDescription ?? self::defaultHomepageDescription();
    }

    public function faviconUrl(): string
    {
        if ($this->faviconPath !== null) {
            return $this->assetUrl($this->faviconPath);
        }

        return asset((string) config('branding.default_favicon', '/favicon.svg'));
    }

    public function logoUrl(): ?string
    {
        if ($this->siteLogoPath !== null) {
            return $this->assetUrl($this->siteLogoPath);
        }

        $default = config('branding.default_logo');
        if (is_string($default) && trim($default) !== '') {
            return asset(ltrim($default, '/'));
        }

        return null;
    }

    public function hasCustomLogo(): bool
    {
        return $this->logoUrl() !== null;
    }

    public function homepageOgImageUrl(): ?string
    {
        if ($this->homepageOgImagePath !== null) {
            return $this->assetUrl($this->homepageOgImagePath);
        }

        $og = config('seo.og_image');
        if (is_string($og) && trim($og) !== '') {
            return trim($og);
        }

        return null;
    }

    private function assetUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
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
