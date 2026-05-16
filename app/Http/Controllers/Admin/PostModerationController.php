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
    public function index(Request $request): View
    {
        $filter = $request->query('durum', 'pending');
        if (! is_string($filter)) {
            $filter = 'pending';
        }

        $allowed = ['pending', 'approved', 'rejected', 'unpublished', 'all'];
        if (! in_array($filter, $allowed, true)) {
            $filter = 'pending';
        }

        $q = Post::query()
            ->with([
                'user:id,name',
                'city:id,name',
                'district:id,name',
                'category:id,name',
                'moderatedBy:id,name',
            ])
            ->latest();

        if ($filter !== 'all') {
            $q->where('moderation_status', PostModerationStatus::from($filter));
        }

        $posts = $q->paginate(20)->withQueryString();

        return view('admin.moderation.index', [
            'posts' => $posts,
            'statusFilter' => $filter,
        ]);
    }

    public function approve(Request $request, Post $post): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($post->moderation_status === PostModerationStatus::Approved) {
            return redirect()->route('admin.moderation.index', $list)->with('status', __('Zaten yayında.'));
        }

        $previous = $post->moderation_status;

        $post->forceFill([
            'moderation_status' => PostModerationStatus::Approved,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => null,
        ])->save();

        $message = $previous === PostModerationStatus::Pending
            ? __('Şikâyet yayına alındı.')
            : __('Şikâyet tekrar yayına alındı.');

        return redirect()->route('admin.moderation.index', $list)->with('status', $message);
    }

    public function reject(Request $request, Post $post): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($post->moderation_status === PostModerationStatus::Approved) {
            return redirect()->route('admin.moderation.index', $list)->with('status', __('Yayındaki şikâyeti kaldırmak için «Yayından kaldır» kullanın.'));
        }

        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $post->forceFill([
            'moderation_status' => PostModerationStatus::Rejected,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Politikaya uygun değil.'),
        ])->save();

        return redirect()->route('admin.moderation.index', $list)->with('status', __('Şikâyet yayına alınmadı.'));
    }

    public function unpublish(Request $request, Post $post): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($post->moderation_status !== PostModerationStatus::Approved) {
            return redirect()->route('admin.moderation.index', $list)->with('status', __('Yalnızca yayındaki şikâyet yayından kaldırılabilir.'));
        }

        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $post->forceFill([
            'moderation_status' => PostModerationStatus::Unpublished,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Yönetici tarafından yayından kaldırıldı.'),
        ])->save();

        return redirect()->route('admin.moderation.index', $list)->with('status', __('Şikâyet yayından kaldırıldı.'));
    }

    private function moderationListFilter(Request $request): string
    {
        $durum = $request->input('durum', $request->query('durum', 'pending'));
        if (! is_string($durum)) {
            return 'pending';
        }

        $allowed = ['pending', 'approved', 'rejected', 'unpublished', 'all'];

        return in_array($durum, $allowed, true) ? $durum : 'pending';
    }
}
