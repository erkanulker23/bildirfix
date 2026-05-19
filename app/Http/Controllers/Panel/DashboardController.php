<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user !== null && $user->canAccessAdminPanel()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = (int) $user->id;

        $recentPosts = Post::query()
            ->where('user_id', $userId)
            ->with(['city:id,name', 'category:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentCampaigns = Campaign::query()
            ->where('user_id', $userId)
            ->with(['city:id,name', 'topic:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('panel.dashboard', [
            'user' => $user,
            'postsCount' => Post::query()->where('user_id', $userId)->count(),
            'campaignsCount' => Campaign::query()->where('user_id', $userId)->count(),
            'postsViewsSum' => (int) Post::query()->where('user_id', $userId)->sum('view_count'),
            'campaignsViewsSum' => (int) Campaign::query()->where('user_id', $userId)->sum('view_count'),
            'recentPosts' => $recentPosts,
            'recentCampaigns' => $recentCampaigns,
        ]);
    }
}
