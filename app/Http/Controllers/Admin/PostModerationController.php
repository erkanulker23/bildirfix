<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostModerationController extends Controller
{
    public function index(): View
    {
        $posts = Post::query()
            ->where('moderation_status', PostModerationStatus::Pending)
            ->with(['user:id,name', 'city:id,name', 'district:id,name', 'category:id,name'])
            ->latest()
            ->paginate(20);

        return view('admin.moderation.index', [
            'posts' => $posts,
        ]);
    }

    public function approve(Request $request, Post $post): RedirectResponse
    {
        if ($post->moderation_status === PostModerationStatus::Approved) {
            return back()->with('status', __('Zaten yayında.'));
        }

        $post->forceFill([
            'moderation_status' => PostModerationStatus::Approved,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => null,
        ])->save();

        return back()->with('status', __('Şikâyet yayına alındı.'));
    }

    public function reject(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $post->forceFill([
            'moderation_status' => PostModerationStatus::Rejected,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Politikaya uygun değil.'),
        ])->save();

        return back()->with('status', __('Şikâyet yayına alınmadı.'));
    }
}
