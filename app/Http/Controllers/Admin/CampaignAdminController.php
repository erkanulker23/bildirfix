<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignTopic;
use App\Models\City;
use App\Support\CampaignDraftBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class CampaignAdminController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('durum', 'all');
        if (! is_string($filter)) {
            $filter = 'all';
        }

        $allowed = ['all', 'pending', 'approved', 'rejected', 'unpublished'];
        if (! in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $q = trim((string) $request->query('q', ''));

        $query = Campaign::query()
            ->with(['user:id,name,email', 'city:id,name', 'moderatedBy:id,name'])
            ->orderByDesc('created_at');

        if ($filter !== 'all') {
            $query->where('moderation_status', CampaignModerationStatus::from($filter));
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like, $q): void {
                $w->where('title', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('email', 'like', $like));
            });
        }

        $campaigns = $query->paginate(25)->withQueryString();

        return view('admin.campaigns.registry', [
            'campaigns' => $campaigns,
            'statusFilter' => $filter,
            'searchQuery' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.campaigns.create', [
            'campaignTopics' => CampaignTopic::query()->orderBy('sort_order')->get(['id', 'name', 'group_key']),
            'topicGroups' => config('campaign_topics.groups', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'purpose' => ['required', 'string', 'min:10', 'max:2000'],
            'personal_story' => ['nullable', 'string', 'max:5000'],
            'scope' => ['required', Rule::in(['local', 'national', 'global'])],
            'campaign_topic_id' => ['required', 'integer', 'exists:campaign_topics,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id', 'required_if:scope,local'],
            'title' => ['required', 'string', 'max:140'],
            'goal_supporters' => ['nullable', 'integer', 'min:10', 'max:2147483646'],
            'hero_image_url' => ['nullable', 'url', 'max:2000'],
        ]);

        $composed = CampaignDraftBuilder::compose($data);
        $base = Campaign::slugFromTitle($composed['title']);
        $slug = Campaign::uniqueSlug($base);

        $campaign = Campaign::query()->create([
            'user_id' => (int) $request->user()->id,
            'title' => $composed['title'],
            'slug' => $slug,
            'excerpt' => $composed['excerpt'],
            'description' => $composed['description'],
            'hero_image_url' => isset($data['hero_image_url']) ? (string) $data['hero_image_url'] : null,
            'city_id' => $composed['city_id'],
            'campaign_topic_id' => (int) $data['campaign_topic_id'],
            'goal_supporters' => $data['goal_supporters'] ?? null,
            'moderation_status' => CampaignModerationStatus::Pending,
        ]);

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('status', __('Kampanya oluşturuldu. Moderasyon durumunu buradan güncelleyebilirsiniz.'));
    }

    public function edit(Campaign $campaign): View
    {
        $cities = City::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.campaigns.edit', [
            'campaign' => $campaign->load('topic:id,name'),
            'cities' => $cities,
            'topics' => CampaignTopic::query()->orderBy('sort_order')->get(['id', 'name']),
            'statuses' => CampaignModerationStatus::cases(),
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'campaign_topic_id' => ['nullable', 'integer', 'exists:campaign_topics,id'],
            'goal_supporters' => ['nullable', 'integer', 'min:0'],
            'moderation_status' => ['required', Rule::enum(CampaignModerationStatus::class)],
            'moderation_note' => ['nullable', 'string', 'max:2000'],
            'hero_image_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $slugBase = Campaign::slugFromTitle($data['title']);
        $data['slug'] = Campaign::uniqueSlug($slugBase, $campaign->id);
        $data['city_id'] = $data['city_id'] ?: null;
        $data['campaign_topic_id'] = $data['campaign_topic_id'] ?: null;
        $data['goal_supporters'] = $data['goal_supporters'] ?? 0;

        $campaign->update($data);

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('status', __('Kampanya güncellendi.'));
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:campaigns,id'],
        ]);

        Campaign::query()->whereIn('id', $data['ids'])->delete();

        return redirect()
            ->route('admin.campaigns.registry', $request->only('durum', 'q', 'page'))
            ->with('status', __('Seçili kampanyalar silindi.'));
    }
}
