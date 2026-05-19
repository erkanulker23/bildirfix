<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyPostsController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->where('user_id', $request->user()->id)
            ->with(['city:id,name', 'category:id,name'])
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('panel.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function edit(Request $request, Post $post): View
    {
        $this->authorizePost($request, $post);

        return view('panel.posts.edit', [
            'post' => $post,
            'canEdit' => $this->canEditPost($post),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorizePost($request, $post);

        if (! $this->canEditPost($post)) {
            return redirect()
                ->route('panel.posts.edit', $post)
                ->with('status', __('Yayındaki bildirimler düzenlenemez; yalnızca görüntüleyebilirsiniz.'));
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:8000'],
        ]);

        $post->fill($data);

        if (in_array($post->moderation_status, [PostModerationStatus::Rejected, PostModerationStatus::Unpublished], true)) {
            $post->moderation_status = PostModerationStatus::Pending;
            $post->moderated_at = null;
            $post->moderated_by_user_id = null;
            $post->moderation_note = null;
        }

        $post->save();

        return redirect()
            ->route('panel.posts.index')
            ->with('status', __('Bildirim güncellendi.'));
    }

    private function authorizePost(Request $request, Post $post): void
    {
        if ((int) $post->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }

    private function canEditPost(Post $post): bool
    {
        return in_array($post->moderation_status, [
            PostModerationStatus::Pending,
            PostModerationStatus::Rejected,
        ], true);
    }
}
