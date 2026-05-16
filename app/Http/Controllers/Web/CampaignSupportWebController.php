<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignSupporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignSupportWebController extends Controller
{
    public function __invoke(Request $request, Campaign $campaign): RedirectResponse
    {
        if (! $campaign->isPubliclyApproved()) {
            abort(404);
        }

        if ($campaign->ends_at !== null && $campaign->ends_at->isPast()) {
            return back()->with('status', __('Bu kampanya süresi dolmuş; destek alınamıyor.'));
        }

        $existing = CampaignSupporter::query()
            ->where('user_id', $request->user()->id)
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', __('Desteğini geri çektin.'));
        }

        CampaignSupporter::query()->create([
            'user_id' => $request->user()->id,
            'campaign_id' => $campaign->id,
        ]);

        return back()->with('status', __('Destek verdin — toplumsal kampanyayı büyüttün, teşekkürler!'));
    }
}
