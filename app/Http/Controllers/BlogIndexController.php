<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\Seo;
use Illuminate\View\View;

final class BlogIndexController extends Controller
{
    public function __invoke(): View
    {
        $activeCategory = null;
        $categorySlug = trim((string) request()->query('kategori', ''));
        if ($categorySlug !== '') {
            $activeCategory = BlogCategory::query()->where('slug', $categorySlug)->first();
        }

        $postsQuery = BlogPost::query()
            ->visibleOnPublicSite()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->orderByDesc('published_at');

        if ($activeCategory !== null) {
            $postsQuery->where('blog_category_id', $activeCategory->id);
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        $categories = BlogCategory::query()
            ->withCount(['posts' => fn ($q) => $q->visibleOnPublicSite()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn (BlogCategory $c) => $c->posts_count > 0);

        $canonical = route('blog.index', array_filter(['kategori' => $activeCategory?->slug]), true);
        $seo = [
            'description' => $activeCategory !== null
                ? __(':cat — blog yazıları.', ['cat' => $activeCategory->name])
                : __('Kent, çevre ve platform haberleri — güncel yazılar.'),
            'canonical' => $canonical,
            'og_title' => ($activeCategory !== null ? $activeCategory->name.' • ' : '').__('Blog').' • '.config('app.name'),
            'og_type' => 'website',
        ];

        $breadcrumbItems = [
            [config('app.name'), route('home', [], true)],
            [__('Blog'), route('blog.index', [], true)],
        ];
        if ($activeCategory !== null) {
            $breadcrumbItems[] = [$activeCategory->name, $canonical];
        }

        $structuredData = [
            Seo::breadcrumbStructuredData($breadcrumbItems),
        ];

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'seo' => $seo,
            'structuredData' => $structuredData,
            'hidePageHero' => true,
        ]);
    }
}
