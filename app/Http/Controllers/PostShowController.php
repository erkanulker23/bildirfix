<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\ContentViewRecorder;
use App\Support\PageHero;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostShowController extends Controller
{
    public function __invoke(Request $request, Post $post): View|Response
    {
        if (! $post->isVisibleTo($request->user())) {
            abort(404);
        }

        ContentViewRecorder::record($post, 'viewed_post');

        $post->load([
            'user:id,name,avatar_path,verification_status',
            'category:id,name',
            'city:id,name,slug',
            'district:id,name',
            'neighborhood:id,name',
            'institution:id,name,verified',
            'institutions:id,name,verified',
            'moderatedBy:id,name',
        ]);
        if (Auth::check()) {
            $uid = Auth::id();
            $post->setAttribute(
                'viewer_supported',
                $post->supports()->where('user_id', $uid)->exists()
            );
            $post->setAttribute(
                'viewer_following',
                $post->follows()->where('user_id', $uid)->exists()
            );
        }

        $comments = $post->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(perPage: 25);

        $canonical = route('posts.show', $post, absolute: true);
        $description = Seo::plainExcerpt($post->description ?: $post->title);

        $seo = [
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $post->title,
            'og_type' => 'article',
        ];

        if (! $post->isPubliclyApproved()) {
            $seo['robots'] = 'noindex, nofollow';
        }

        $structuredData = [];
        if ($post->isPubliclyApproved()) {
            $structuredData[] = Seo::complaintArticleStructuredData($post);
            $structuredData[] = Seo::breadcrumbStructuredData([
                [config('app.name'), route('home', [], true)],
                [Str::limit($post->title, 100), $canonical],
            ]);
        }

        $supportUsers = collect();
        $followUsers = collect();
        if ($post->isPubliclyApproved()) {
            $supportUsers = $post->supports()
                ->with('user:id,name')
                ->latest()
                ->limit(80)
                ->get()
                ->map(static fn ($row) => $row->user)
                ->filter()
                ->unique('id')
                ->take(28)
                ->values();

            $followUsers = $post->follows()
                ->with('user:id,name')
                ->latest()
                ->limit(80)
                ->get()
                ->map(static fn ($row) => $row->user)
                ->filter()
                ->unique('id')
                ->take(28)
                ->values();
        }

        $hero = PageHero::fromTitle(
            $post->title,
            $post->category?->name ?? __('Bildirim'),
            Seo::plainExcerpt($post->description ?: $post->title),
        );

        return view('posts.show', [
            'post' => $post,
            'comments' => $comments,
            'seo' => $seo,
            'structuredData' => $structuredData,
            'supportUsers' => $supportUsers,
            'followUsers' => $followUsers,
            'pageHero' => $hero,
        ]);
    }
}
