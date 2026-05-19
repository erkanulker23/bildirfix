<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\Seo;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class BlogShowController extends Controller
{
    public function __invoke(string $slug): View
    {
        $post = BlogPost::query()
            ->visibleOnPublicSite()
            ->where('slug', $slug)
            ->with(['author:id,name', 'category:id,name,slug'])
            ->firstOrFail();

        $canonical = route('blog.show', ['slug' => $post->slug], absolute: true);
        $description = $post->meta_description
            ?: ($post->excerpt ? Seo::plainExcerpt($post->excerpt, 160) : Seo::plainExcerpt($post->body));

        $seo = [
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $post->meta_title ?: $post->title,
            'og_type' => 'article',
        ];

        $hero = trim((string) ($post->hero_image_url ?? ''));
        if ($hero !== '') {
            $seo['og_image'] = Str::startsWith($hero, ['http://', 'https://'])
                ? $hero
                : url(ltrim($hero, '/'));
        }
        $structuredData = [
            Seo::blogArticleStructuredData($post),
            Seo::breadcrumbStructuredData([
                [config('app.name'), route('home', [], true)],
                [__('Blog'), route('blog.index', [], true)],
                [Str::limit($post->title, 90), $canonical],
            ]),
        ];

        return view('blog.show', [
            'post' => $post,
            'hidePageHero' => true,
            'shareUrl' => $canonical,
            'shareTitle' => $post->title,
            'seo' => $seo,
            'structuredData' => $structuredData,
        ]);
    }
}
