<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\Seo;
use Illuminate\View\View;

final class BlogIndexController extends Controller
{
    public function __invoke(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->with('author:id,name')
            ->paginate(12);

        $canonical = route('blog.index', [], true);
        $seo = [
            'description' => __('Kent, çevre ve platform haberleri — güncel yazılar.'),
            'canonical' => $canonical,
            'og_title' => __('Blog').' • '.config('app.name'),
            'og_type' => 'website',
        ];

        $structuredData = [
            Seo::breadcrumbStructuredData([
                [config('app.name'), route('home', [], true)],
                [__('Blog'), $canonical],
            ]),
        ];

        return view('blog.index', [
            'posts' => $posts,
            'seo' => $seo,
            'structuredData' => $structuredData,
        ]);
    }
}
