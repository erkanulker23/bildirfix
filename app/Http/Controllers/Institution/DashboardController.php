<?php

declare(strict_types=1);

namespace App\Http\Controllers\Institution;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $institution = auth()->user()?->managedInstitution;

        $stats = [
            'total' => 0,
            'open' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'views' => 0,
            'supports' => 0,
        ];

        $recentPosts = collect();
        $byCity = collect();

        if ($institution !== null) {
            $baseQuery = $this->postsForInstitution((int) $institution->id);

            $stats['total'] = (clone $baseQuery)->count();
            $stats['open'] = (clone $baseQuery)->where('status', PostStatus::Open)->count();
            $stats['in_progress'] = (clone $baseQuery)->where('status', PostStatus::InProgress)->count();
            $stats['resolved'] = (clone $baseQuery)->where('status', PostStatus::Resolved)->count();
            $stats['views'] = (int) (clone $baseQuery)->sum('view_count');
            $stats['supports'] = (int) (clone $baseQuery)->sum('support_count');

            $recentPosts = (clone $baseQuery)
                ->with(['city:id,name', 'district:id,name', 'category:id,name', 'user:id,name'])
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            $byCity = (clone $baseQuery)
                ->join('cities', 'posts.city_id', '=', 'cities.id')
                ->selectRaw('cities.name as city_name, COUNT(*) as total')
                ->groupBy('cities.name')
                ->orderByDesc('total')
                ->limit(6)
                ->get();
        }

        return view('institution.dashboard', [
            'user' => auth()->user(),
            'institution' => $institution,
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'byCity' => $byCity,
        ]);
    }

    /**
     * @return Builder<Post>
     */
    private function postsForInstitution(int $institutionId): Builder
    {
        return Post::query()
            ->where('moderation_status', PostModerationStatus::Approved)
            ->where(function (Builder $q) use ($institutionId): void {
                $q->where('institution_id', $institutionId)
                    ->orWhereHas('institutions', fn (Builder $iq) => $iq->where('institutions.id', $institutionId));
            });
    }
}
