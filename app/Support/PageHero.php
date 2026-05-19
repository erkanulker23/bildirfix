<?php

declare(strict_types=1);

namespace App\Support;

final class PageHero
{
    /**
     * @return array{overline: string|null, title: string, titleAccent: string|null, description: string|null}|null
     */
    public static function forRoute(?string $routeName): ?array
    {
        if ($routeName === null || self::shouldSkip($routeName)) {
            return null;
        }

        return match ($routeName) {
            'campaigns.index' => null,
            'campaigns.create' => self::make(
                __('Yeni kampanya'),
                __('Kampanya'),
                __('Başlatın'),
                __('Toplumsal farkındalık için kampanyanızı oluşturun; yayın öncesi moderasyon sürecinden geçer.'),
            ),
            'feed.index' => null,
            'cities.explore' => self::make(
                __('Türkiye geneli'),
                __('Şehrini'),
                __('Keşfet'),
                __('İl ve ilçe bazında bildirimleri inceleyin; kendi şehrinizde neler konuşulduğunu görün.'),
            ),
            'blog.index' => null,
            'contact' => self::make(
                __('İletişim'),
                __('Bize'),
                __('Yazın'),
                __('Sorularınız ve geri bildirimleriniz için formu kullanabilirsiniz.'),
            ),
            'how-it-works' => null,
            'legal.privacy' => self::make(
                __('Yasal'),
                __('Gizlilik'),
                __('Politikası'),
                __('Kişisel verilerinizin işlenmesine ilişkin metin.'),
            ),
            'legal.kvkk' => self::make(
                __('Yasal'),
                __('KVKK'),
                null,
                __('Kişisel verilerin korunması ve başvuru süreçleri.'),
            ),
            'legal.terms' => self::make(
                __('Yasal'),
                __('Kullanım'),
                __('Koşulları'),
                __('Platformu kullanırken geçerli kurallar ve sorumluluklar.'),
            ),
            'posts.create' => self::make(
                __('Yeni bildirim'),
                __('Kent Sorunu'),
                __('Bildir'),
                __('Fotoğraf ve konum ile sorunu paylaşın; moderasyon sonrası yayına alınır.'),
            ),
            'profile' => self::make(
                __('Hesabım'),
                __('Profilim'),
                null,
                __('Paylaşımlarınız ve hesap bilgileriniz.'),
            ),
            'notifications.index' => self::make(
                __('Hesabım'),
                __('Bildirimler'),
                null,
                __('Destek, yorum ve süreç güncellemeleri.'),
            ),
            'cities.show' => null,
            'campaigns.show' => null,
            'posts.show' => null,
            'blog.show' => null,
            'institutions.show' => null,
            default => self::make(
                config('app.name'),
                __('Kent'),
                __('Bildirimi'),
                __('Şehir sorunlarını görünür kılın, destek olun ve süreci takip edin.'),
            ),
        };
    }

    /**
     * @return array{overline: string|null, title: string, titleAccent: string|null, description: string|null}
     */
    public static function fromTitle(
        string $title,
        ?string $overline = null,
        ?string $description = null,
        ?string $titleAccent = null,
    ): array {
        if ($titleAccent === null) {
            ['title' => $title, 'titleAccent' => $titleAccent] = self::splitTitleAccent($title);
        }

        return self::make($overline, $title, $titleAccent, $description);
    }

    /**
     * @return array{title: string, titleAccent: string|null}
     */
    public static function splitTitleAccent(string $fullTitle): array
    {
        $fullTitle = trim($fullTitle);
        $words = preg_split('/\s+/u', $fullTitle) ?: [];

        if (count($words) <= 1) {
            return ['title' => $fullTitle, 'titleAccent' => null];
        }

        $accent = (string) array_pop($words);

        return [
            'title' => implode(' ', $words),
            'titleAccent' => $accent,
        ];
    }

    private static function shouldSkip(string $routeName): bool
    {
        return in_array($routeName, ['home'], true)
            || str_starts_with($routeName, 'login')
            || str_starts_with($routeName, 'register')
            || str_starts_with($routeName, 'password.')
            || str_starts_with($routeName, 'verify.')
            || str_starts_with($routeName, 'auth.');
    }

    /**
     * @return array{overline: string|null, title: string, titleAccent: string|null, description: string|null}
     */
    public static function make(
        ?string $overline,
        string $title,
        ?string $titleAccent,
        ?string $description,
    ): array {
        return [
            'overline' => $overline,
            'title' => $title,
            'titleAccent' => $titleAccent,
            'description' => $description,
        ];
    }
}
