<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignModerationStatus;
use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Campaign;
use App\Models\Institution;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = request()->user();

        return view('admin.dashboard', [
            'usersCount' => User::query()->count(),
            'postsCount' => Post::query()->count(),
            'institutionsCount' => Institution::query()->count(),
            'campaignsCount' => Campaign::query()->count(),
            'approvedPostsCount' => Post::query()->publicApproved()->count(),
            'approvedCampaignsCount' => Campaign::query()->publicApproved()->count(),
            'openPosts' => Post::query()->where('status', 'open')->count(),
            'pendingModeration' => Post::query()
                ->where('moderation_status', PostModerationStatus::Pending)
                ->count(),
            'pendingCampaignModeration' => Campaign::query()
                ->where('moderation_status', CampaignModerationStatus::Pending)
                ->count(),
            'pendingBlogModeration' => BlogPost::query()
                ->where('moderation_status', PostModerationStatus::Pending)
                ->count(),
            'blogTotalCount' => BlogPost::query()->count(),
            'blogLiveCount' => BlogPost::query()->visibleOnPublicSite()->count(),
            'viewerIsSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }
}
