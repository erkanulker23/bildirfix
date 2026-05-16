<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CampaignShowController extends Controller
{
    public function __invoke(Request $request, Campaign $campaign): View
    {
        if (! $campaign->isVisibleTo($request->user())) {
            abort(404);
        }

        $campaign->load(['user:id,name', 'city:id,name', 'moderatedBy:id,name']);

        if (Auth::check()) {
            $uid = Auth::id();
            $campaign->setAttribute(
                'viewer_supports',
                $campaign->supporters()->where('user_id', $uid)->exists()
            );
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
                [\Illuminate\Support\Str::limit($campaign->title, 100), $canonical],
            ]);
        }

        return view('campaigns.show', [
            'campaign' => $campaign,
            'seo' => $seo,
            'structuredData' => $structuredData,
        ]);
    }
}
