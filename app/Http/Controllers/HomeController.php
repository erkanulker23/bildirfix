<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\City;
use App\Models\Institution;
use App\Models\Post;
use App\Models\User;
use App\Support\HomeSpotlightFeed;
use App\Support\PublicPostFeed;
use App\Support\PublicStoryFeed;
use App\Support\Seo;
use App\Support\SiteBranding;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $ctx = PublicPostFeed::locationContext(request());
        $cityId = $ctx['cityId'];
        $lat = $ctx['lat'];
        $lng = $ctx['lng'];
        $relaxNearby = $ctx['relaxNearby'];

        $stories = PublicStoryFeed::forRequest(request(), 42);

        $published = Post::query()->publicApproved();
        $publishedTotal = (clone $published)->count();
        $withEvidenceCount = (clone $published)->where(function ($q): void {
            $q->where(function ($u): void {
                $u->whereNotNull('media_url')->where('media_url', '!=', '');
            })->orWhere(function ($u): void {
                $u->whereNotNull('latitude')->whereNotNull('longitude');
            });
        })->count();

        $evidencePct = $publishedTotal > 0 ? min(100, (int) round(100 * $withEvidenceCount / $publishedTotal)) : 0;

        $platformStats = [
            'members' => User::query()->whereIn('role', [UserRole::User, UserRole::VerifiedUser])->count(),
            'brands' => Institution::query()->count(),
            'resolved' => max(0, (clone $published)->where('status', PostStatus::Resolved)->count()),
            'evidence_pct' => $evidencePct,
            'last30' => max(0, (clone $published)->where('created_at', '>=', now()->subDays(30))->count()),
            'published' => max(0, $publishedTotal),
            'campaigns_live' => max(0, Campaign::query()->publicApproved()->count()),
        ];

        $featuredCampaignQuery = Campaign::query()
            ->publicApproved()
            ->with(['city:id,name']);

        if ($cityId !== null && (int) $cityId !== 0) {
            $featuredCampaignQuery->where(function ($w) use ($cityId): void {
                $w->whereNull('city_id')->orWhere('city_id', (int) $cityId);
            });
        }

        $featuredCampaigns = $featuredCampaignQuery->orderByDesc('supporter_count')->take(16)->get();

        $spotlight = HomeSpotlightFeed::forRequest(request());
        $spotlightVideoPosts = $spotlight['videos'];
        $spotlightImagePosts = $spotlight['images'];
        $spotlightStories = $spotlight['stories'];

        $cityComplaintSub = Post::query()
            ->publicApproved()
            ->whereNotNull('city_id')
            ->selectRaw('city_id, COUNT(*) as complaint_count')
            ->groupBy('city_id');

        $topCitiesByComplaints = City::query()
            ->joinSub($cityComplaintSub, 'post_counts', 'cities.id', '=', 'post_counts.city_id')
            ->orderByDesc('post_counts.complaint_count')
            ->orderBy('cities.name')
            ->limit(10)
            ->select('cities.id', 'cities.name', 'cities.slug', 'cities.plate', 'post_counts.complaint_count')
            ->get();

        $approved = PostModerationStatus::Approved->value;

        $legacyLinks = DB::table('posts')
            ->where('moderation_status', $approved)
            ->whereNotNull('institution_id')
            ->selectRaw('institution_id as institution_id, id as post_id');

        $pivotLinks = DB::table('institution_post')
            ->join('posts', 'posts.id', '=', 'institution_post.post_id')
            ->where('posts.moderation_status', $approved)
            ->selectRaw('institution_post.institution_id as institution_id, posts.id as post_id');

        $institutionComplaintSub = DB::query()
            ->fromSub($legacyLinks->union($pivotLinks), 'institution_links')
            ->groupBy('institution_links.institution_id')
            ->selectRaw('institution_links.institution_id, COUNT(DISTINCT institution_links.post_id) as complaint_count');

        $topInstitutionsByComplaints = Institution::query()
            ->joinSub($institutionComplaintSub, 'post_counts', 'institutions.id', '=', 'post_counts.institution_id')
            ->orderByDesc('post_counts.complaint_count')
            ->orderBy('institutions.name')
            ->limit(10)
            ->select('institutions.id', 'institutions.name', 'institutions.verified', 'institutions.city_id', 'institutions.logo_url', 'post_counts.complaint_count')
            ->with(['city:id,name'])
            ->get();

        $trendingComplaints = Post::query()
            ->publicApproved()
            ->with(['user:id,name,verification_status', 'institution:id,name,verified', 'institutions:id,name,verified', 'city:id,name', 'category:id,name'])
            ->orderByRaw('(posts.support_count * 10 + posts.comments_count * 6 + COALESCE(posts.follow_count, 0) * 8) DESC')
            ->orderByDesc('posts.created_at')
            ->limit(16)
            ->get()
            ->shuffle()
            ->values();

        $branding = SiteBranding::fromPlatform();

        return view('home', [
            'stories' => $stories,
            'featuredCampaigns' => $featuredCampaigns,
            'spotlightVideoPosts' => $spotlightVideoPosts,
            'spotlightImagePosts' => $spotlightImagePosts,
            'spotlightStories' => $spotlightStories,
            'topCitiesByComplaints' => $topCitiesByComplaints,
            'topInstitutionsByComplaints' => $topInstitutionsByComplaints,
            'trendingComplaints' => $trendingComplaints,
            'platformStats' => $platformStats,
            'activeCityId' => $cityId,
            'searchQuery' => (string) request('q', ''),
            'nearLat' => $lat,
            'nearLng' => $lng,
            'geoActive' => $lat !== null && $lng !== null,
            'relaxNearby' => $relaxNearby,
            'seo' => [
                'description' => $branding->homepageDescription(),
                'canonical' => request()->fullUrl(),
                'og_title' => $branding->homepageTitle(),
                'og_type' => 'website',
                'og_image' => $branding->homepageOgImageUrl(),
            ],
            'structuredData' => [
                Seo::webSiteStructuredData(),
            ],
        ]);
    }
}
