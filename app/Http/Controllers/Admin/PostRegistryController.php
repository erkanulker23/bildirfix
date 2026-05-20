<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PostRegistryController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('durum', 'all');
        if (! is_string($filter)) {
            $filter = 'all';
        }

        $allowed = ['all', 'pending', 'approved', 'rejected', 'unpublished', 'imported'];
        if (! in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $q = trim((string) $request->query('q', ''));

        $query = Post::query()
            ->where('type', 'complaint')
            ->with([
                'user:id,name,email',
                'city:id,name',
                'institution:id,name',
                'externalImportSource:id,name',
            ])
            ->orderByDesc('created_at');

        if ($filter === 'imported') {
            $query->whereNotNull('external_source');
        } elseif ($filter !== 'all') {
            $query->where('moderation_status', PostModerationStatus::from($filter));
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like): void {
                $w->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('source_url', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like));
            });
        }

        $posts = $query->paginate(25)->withQueryString();

        return view('admin.posts.registry', [
            'posts' => $posts,
            'statusFilter' => $filter,
            'searchQuery' => $q,
        ]);
    }
}
