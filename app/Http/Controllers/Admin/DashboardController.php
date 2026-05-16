<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Enums\CampaignModerationStatus;
use App\Models\Campaign;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var \App\Models\User $user */
        $user = request()->user();

        return view('admin.dashboard', [
            'usersCount' => User::query()->count(),
            'postsCount' => Post::query()->count(),
            'openPosts' => Post::query()->where('status', 'open')->count(),
            'pendingModeration' => Post::query()
                ->where('moderation_status', PostModerationStatus::Pending)
                ->count(),
            'pendingCampaignModeration' => Campaign::query()
                ->where('moderation_status', CampaignModerationStatus::Pending)
                ->count(),
            'viewerIsSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }
}
