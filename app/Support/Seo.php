<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Post;
use Illuminate\Support\Str;

final class Seo
{
    /**
     * @return array<string, mixed>
     */
    public static function organizationStructuredData(?string $name = null): array
    {
        $organizationName = $name ?: config('seo.organization_name') ?: config('app.name');
        $url = rtrim((string) config('app.url'), '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $organizationName,
            'url' => $url,
            'logo' => $url.'/favicon.ico',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function webSiteStructuredData(): array
    {
        $url = rtrim((string) config('app.url'), '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => $url,
            'name' => config('app.name'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('home', [], true).'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function breadcrumbStructuredData(array $levels): array
    {
        $items = [];

        foreach ($levels as $i => [$name, $itemUrl]) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $name,
                'item' => $itemUrl,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function complaintArticleStructuredData(Post $post): array
    {
        $url = route('posts.show', $post, absolute: true);
        $description = $post->description
            ? Str::limit(strip_tags((string) $post->description), 300)
            : $post->title;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $description,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'datePublished' => $post->created_at?->toIso8601String(),
            'dateModified' => ($post->updated_at ?? $post->created_at)?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->user?->name ?: __('Anonim'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.organization_name') ?: config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => rtrim((string) config('app.url'), '/').'/favicon.ico',
                ],
            ],
        ];
    }

    public static function blogArticleStructuredData(BlogPost $post): array
    {
        $url = route('blog.show', ['slug' => $post->slug], absolute: true);
        $description = $post->meta_description
            ?: ($post->excerpt ? self::plainExcerpt($post->excerpt, 300) : self::plainExcerpt($post->body, 300));

        $article = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->meta_title ?: $post->title,
            'description' => $description,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url,
            ],
            'datePublished' => ($post->published_at ?? $post->created_at)?->toIso8601String(),
            'dateModified' => ($post->updated_at ?? $post->published_at ?? $post->created_at)?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author?->name ?: config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('seo.organization_name') ?: config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => rtrim((string) config('app.url'), '/').'/favicon.ico',
                ],
            ],
        ];

        $hero = trim((string) ($post->hero_image_url ?? ''));
        if ($hero !== '') {
            $article['image'] = [Str::startsWith($hero, ['http://', 'https://']) ? $hero : url(ltrim($hero, '/'))];
        }

        return $article;
    }

    /** İç bağlantılar için güvenilir metin çıkarımı */
    public static function plainExcerpt(?string $htmlText, int $limit = 160): string
    {
        return Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags((string) $htmlText)) ?: ''), $limit);
    }
}
