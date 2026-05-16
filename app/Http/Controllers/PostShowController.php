<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PostShowController extends Controller
{
    public function __invoke(Request $request, Post $post): View|\Illuminate\Http\Response
    {
        if (! $post->isVisibleTo($request->user())) {
            abort(404);
        }

        $post->load([
            'user:id,name',
            'category:id,name',
            'city:id,name',
            'district:id,name',
            'neighborhood:id,name',
            'institution:id,name,verified',
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
                [\Illuminate\Support\Str::limit($post->title, 100), $canonical],
            ]);
        }

        return view('posts.show', [
            'post' => $post,
            'comments' => $comments,
            'seo' => $seo,
            'structuredData' => $structuredData,
        ]);
    }
}
