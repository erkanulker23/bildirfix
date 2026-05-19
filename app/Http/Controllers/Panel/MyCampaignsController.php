<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Enums\CampaignModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignTopic;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyCampaignsController extends Controller
{
    public function index(Request $request): View
    {
        $campaigns = Campaign::query()
            ->where('user_id', $request->user()->id)
            ->with(['city:id,name', 'topic:id,name'])
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('panel.campaigns.index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function edit(Request $request, Campaign $campaign): View
    {
        $this->authorizeCampaign($request, $campaign);

        return view('panel.campaigns.edit', [
            'campaign' => $campaign->load('topic:id,name'),
            'cities' => City::query()->orderBy('name')->get(['id', 'name']),
            'topics' => CampaignTopic::query()->orderBy('sort_order')->get(['id', 'name']),
            'canEdit' => $this->canEditCampaign($campaign),
        ]);
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $this->authorizeCampaign($request, $campaign);

        if (! $this->canEditCampaign($campaign)) {
            return redirect()
                ->route('panel.campaigns.edit', $campaign)
                ->with('status', __('Yayındaki kampanyalar düzenlenemez; yalnızca görüntüleyebilirsiniz.'));
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'excerpt' => ['nullable', 'string', 'max:480'],
            'description' => ['required', 'string', 'max:20000'],
            'hero_image_url' => ['nullable', 'url', 'max:2000'],
            'goal_supporters' => ['nullable', 'integer', 'min:10', 'max:2147483646'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'campaign_topic_id' => ['nullable', 'integer', 'exists:campaign_topics,id'],
        ]);

        $campaign->fill($data);

        if (in_array($campaign->moderation_status, [CampaignModerationStatus::Rejected, CampaignModerationStatus::Unpublished], true)) {
            $campaign->moderation_status = CampaignModerationStatus::Pending;
            $campaign->moderated_at = null;
            $campaign->moderated_by_user_id = null;
            $campaign->moderation_note = null;
        }

        $campaign->save();

        return redirect()
            ->route('panel.campaigns.index')
            ->with('status', __('Kampanya güncellendi.'));
    }

    private function authorizeCampaign(Request $request, Campaign $campaign): void
    {
        if ((int) $campaign->user_id !== (int) $request->user()->id) {
            abort(404);
        }
    }

    private function canEditCampaign(Campaign $campaign): bool
    {
        return in_array($campaign->moderation_status, [
            CampaignModerationStatus::Pending,
            CampaignModerationStatus::Rejected,
        ], true);
    }
}
