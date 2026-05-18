<?php

declare(strict_types=1);

/**
 * Varsayılan değerler Google / Yandex / Bing ortak ilkelerine göre yapılandırılabilir.
 */
return [
    'default_meta_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Şehir şikâyetlerini listeleyin, destek olun ve süreci görünür kılın. Vatandaşların yerel sorun bildirimleri ve kurumsal süreçler için simdibildir.com platformu.',
    ),

    /** Open Graph görsel mutlak URI (HTTPS). Boş ise og:image çıktısı verilmez. */
    'og_image' => env('SEO_OG_IMAGE'),

    /** İletişim / kurumsal yapı için (JSON-LD). */
    'organization_name' => env('SEO_ORG_NAME'),

    /** Twitter kartı için büyük önizleme (önerilen görsel varsa açıldığında daha iyi). */
    'twitter_card' => 'summary_large_image',

    /** Fonts CDN — Web Vitals için bağlantı ipuçları (Bunny Fonts Vite ile birlikte). */
    'preconnect_hints' => array_values(array_filter(explode(',', (string) env('SEO_FONT_PRECONNECT_HOSTS', 'https://fonts.bunny.net')))),

    /** Üretim dışında .env ile noindex kullanın; öneri: SEO_DEFAULT_ROBOTS=noindex, nofollow */
    'default_robots' => env(
        'SEO_DEFAULT_ROBOTS',
        env('APP_ENV', 'production') === 'production'
            ? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'
            : 'noindex, nofollow',
    ),

    /** Yerel yapı için (NewsArticle uygunluğu). */
    'locale_og' => env('SEO_OG_LOCALE', 'tr_TR'),
];
