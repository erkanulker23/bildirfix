<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Support\ContentViewRecorder;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignShowController extends Controller
{
    public function __invoke(Request $request, Campaign $campaign): View
    {
        if (! $campaign->isVisibleTo($request->user())) {
            abort(404);
        }

        ContentViewRecorder::record($campaign, 'viewed_campaign');

        $campaign->load(['user:id,name,avatar_path', 'city:id,name', 'topic:id,name,slug', 'moderatedBy:id,name']);

        $campaignSupporters = $campaign->supporters()
            ->with('user:id,name')
            ->latest('campaign_supporters.created_at')
            ->limit(100)
            ->get()
            ->filter(static fn ($row) => $row->user !== null)
            ->values();

        if (Auth::check()) {
            $uid = Auth::id();
            $campaign->setAttribute(
                'viewer_supports',
                $campaign->supporters()->where('user_id', $uid)->exists()
            );
        }

        $campaignComments = null;
        if ($campaign->isPubliclyApproved()) {
            $campaignComments = $campaign->comments()
                ->with('user:id,name')
                ->latest()
                ->paginate(25)
                ->fragment('yorumlar');
        }

        $canonical = route('campaigns.show', $campaign, absolute: true);
        $excerpt = (string) ($campaign->excerpt ?? '');
        $description = Seo::plainExcerpt($excerpt !== '' ? $excerpt : $campaign->description);

        $seo = [
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $campaign->title,
            'og_type' => 'article',
        ];

        if (! $campaign->isPubliclyApproved()) {
            $seo['robots'] = 'noindex, nofollow';
        }

        $structuredData = [];
        if ($campaign->isPubliclyApproved()) {
            $structuredData[] = Seo::breadcrumbStructuredData([
                [config('app.name'), route('home', [], true)],
                [__('Toplumsal kampanyalar'), route('campaigns.index', [], true)],
                [Str::limit($campaign->title, 100), $canonical],
            ]);
        }

        return view('campaigns.show', [
            'campaign' => $campaign,
            'campaignSupporters' => $campaignSupporters,
            'campaignComments' => $campaignComments,
            'seo' => $seo,
            'structuredData' => $structuredData,
            'hidePageHero' => true,
        ]);
    }
}
