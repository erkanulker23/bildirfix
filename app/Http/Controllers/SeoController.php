<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Post;
use Illuminate\Http\Response;

final class SeoController extends Controller
{
    /** @var positive-int */
    private const POST_SITEMAP_LIMIT = 2000;

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /panel/',
            'Disallow: /institution/',
            'Disallow: /verify-phone',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $base = rtrim((string) config('app.url'), '/');

        $urls = [
            [
                'loc' => $base.'/',
                'changefreq' => 'hourly',
                'priority' => '1.0',
                'lastmod' => now()->startOfMinute()->toAtomString(),
            ],
            [
                'loc' => $base.'/sehirini-kesfet',
                'changefreq' => 'daily',
                'priority' => '0.9',
                'lastmod' => now()->startOfMinute()->toAtomString(),
            ],
            [
                'loc' => $base.'/blog',
                'changefreq' => 'daily',
                'priority' => '0.82',
                'lastmod' => now()->startOfMinute()->toAtomString(),
            ],
            [
                'loc' => $base.'/nasil-calisir',
                'changefreq' => 'monthly',
                'priority' => '0.75',
                'lastmod' => now()->startOfMinute()->toAtomString(),
            ],
        ];

        $blogPosts = BlogPost::query()
            ->published()
            ->orderByDesc('updated_at')
            ->limit(self::POST_SITEMAP_LIMIT)
            ->get(['slug', 'updated_at', 'created_at', 'published_at']);

        foreach ($blogPosts as $blogPost) {
            $urls[] = [
                'loc' => route('blog.show', ['slug' => $blogPost->slug], absolute: true),
                'changefreq' => 'weekly',
                'priority' => '0.78',
                'lastmod' => ($blogPost->updated_at ?? $blogPost->published_at ?? $blogPost->created_at ?? now())->clone()->startOfMinute()->toAtomString(),
            ];
        }

        $posts = Post::query()
            ->publicApproved()
            ->orderByDesc('updated_at')
            ->limit(self::POST_SITEMAP_LIMIT)
            ->get(['id', 'updated_at', 'created_at']);

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('posts.show', $post, absolute: true),
                'changefreq' => 'daily',
                'priority' => '0.72',
                'lastmod' => ($post->updated_at ?? $post->created_at ?? now())->clone()->startOfMinute()->toAtomString(),
            ];
        }

        return response()->view('seo.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
