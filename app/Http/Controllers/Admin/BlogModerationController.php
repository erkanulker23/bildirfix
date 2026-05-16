<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogModerationController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->where('moderation_status', PostModerationStatus::Pending)
            ->with(['author:id,name', 'moderatedBy:id,name'])
            ->latest()
            ->paginate(20);

        return view('admin.moderation.blog', [
            'posts' => $posts,
        ]);
    }

    public function approve(Request $request, BlogPost $blog): RedirectResponse
    {
        if ($blog->moderation_status === PostModerationStatus::Approved) {
            return back()->with('status', __('Zaten onaylı.'));
        }

        $blog->forceFill([
            'moderation_status' => PostModerationStatus::Approved,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => null,
        ]);

        if ($blog->is_published && $blog->published_at === null) {
            $blog->published_at = now();
        }

        $blog->save();

        return back()->with('status', __('Blog yazısı yayına alındı.'));
    }

    public function reject(Request $request, BlogPost $blog): RedirectResponse
    {
        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $blog->forceFill([
            'moderation_status' => PostModerationStatus::Rejected,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Politikaya uygun değil.'),
            'is_published' => false,
            'published_at' => null,
        ])->save();

        return back()->with('status', __('Blog yazısı reddedildi.'));
    }
}
