<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampaignModerationController extends Controller
{
    public function index(): View
    {
        $campaigns = Campaign::query()
            ->where('moderation_status', CampaignModerationStatus::Pending)
            ->with(['user:id,name', 'city:id,name'])
            ->latest()
            ->paginate(perPage: 20);

        return view('admin.moderation.campaigns', compact('campaigns'));
    }

    public function approve(Campaign $campaign): RedirectResponse
    {
        if ($campaign->moderation_status === CampaignModerationStatus::Approved) {
            return back()->with('status', __('Kampanya zaten yayımda.'));
        }

        $campaign->fill([
            'moderation_status' => CampaignModerationStatus::Approved,
            'moderated_at' => now(),
            'moderated_by_user_id' => request()->user()->id,
            'moderation_note' => null,
        ]);
        $campaign->save();

        return back()->with('status', __('Kampanya yayına alındı.'));
    }

    public function reject(Campaign $campaign): RedirectResponse
    {
        $data = request()->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $campaign->fill([
            'moderation_status' => CampaignModerationStatus::Rejected,
            'moderated_at' => now(),
            'moderated_by_user_id' => request()->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Politikaya uygun değil.'),
        ]);
        $campaign->save();

        return back()->with('status', __('Kampanya reddedildi.'));
    }
}
