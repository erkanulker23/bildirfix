<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CampaignModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignModerationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('durum', 'all');
        if (! is_string($filter)) {
            $filter = 'all';
        }

        $allowed = ['pending', 'approved', 'rejected', 'unpublished', 'all'];
        if (! in_array($filter, $allowed, true)) {
            $filter = 'all';
        }

        $q = Campaign::query()
            ->with(['user:id,name', 'city:id,name', 'moderatedBy:id,name'])
            ->latest();

        if ($filter !== 'all') {
            $q->where('moderation_status', CampaignModerationStatus::from($filter));
        }

        $campaigns = $q->paginate(perPage: 20)->withQueryString();

        return view('admin.moderation.campaigns', [
            'campaigns' => $campaigns,
            'statusFilter' => $filter,
        ]);
    }

    public function approve(Request $request, Campaign $campaign): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($campaign->moderation_status === CampaignModerationStatus::Approved) {
            return redirect()->route('admin.campaign-moderation.index', $list)->with('status', __('Kampanya zaten yayımda.'));
        }

        $previous = $campaign->moderation_status;

        $campaign->fill([
            'moderation_status' => CampaignModerationStatus::Approved,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => null,
        ]);
        $campaign->save();

        $message = $previous === CampaignModerationStatus::Pending
            ? __('Kampanya yayına alındı.')
            : __('Kampanya tekrar yayına alındı.');

        return redirect()->route('admin.campaign-moderation.index', $list)->with('status', $message);
    }

    public function reject(Request $request, Campaign $campaign): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($campaign->moderation_status === CampaignModerationStatus::Approved) {
            return redirect()->route('admin.campaign-moderation.index', $list)->with('status', __('Yayındaki kampanyayı kaldırmak için «Yayından kaldır» kullanın.'));
        }

        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $campaign->fill([
            'moderation_status' => CampaignModerationStatus::Rejected,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Politikaya uygun değil.'),
        ]);
        $campaign->save();

        return redirect()->route('admin.campaign-moderation.index', $list)->with('status', __('Kampanya reddedildi.'));
    }

    public function unpublish(Request $request, Campaign $campaign): RedirectResponse
    {
        $list = ['durum' => $this->moderationListFilter($request)];

        if ($campaign->moderation_status !== CampaignModerationStatus::Approved) {
            return redirect()->route('admin.campaign-moderation.index', $list)->with('status', __('Yalnızca yayındaki kampanya yayından kaldırılabilir.'));
        }

        $data = $request->validate([
            'moderation_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $campaign->fill([
            'moderation_status' => CampaignModerationStatus::Unpublished,
            'moderated_at' => now(),
            'moderated_by_user_id' => $request->user()->id,
            'moderation_note' => $data['moderation_note'] ?? __('Yönetici tarafından yayından kaldırıldı.'),
        ]);
        $campaign->save();

        return redirect()->route('admin.campaign-moderation.index', $list)->with('status', __('Kampanya yayından kaldırıldı.'));
    }

    private function moderationListFilter(Request $request): string
    {
        $durum = $request->input('durum', $request->query('durum', 'all'));
        if (! is_string($durum)) {
            return 'all';
        }

        $allowed = ['pending', 'approved', 'rejected', 'unpublished', 'all'];

        return in_array($durum, $allowed, true) ? $durum : 'all';
    }
}
