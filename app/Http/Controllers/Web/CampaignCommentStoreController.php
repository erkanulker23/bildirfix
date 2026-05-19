<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignCommentStoreController extends Controller
{
    public function __invoke(Request $request, Campaign $campaign): RedirectResponse
    {
        if (! $campaign->isVisibleTo($request->user())) {
            abort(404);
        }

        if (! $campaign->isPubliclyApproved()) {
            return redirect()
                ->route('campaigns.show', $campaign)
                ->withFragment('yorumlar')
                ->withErrors(['content' => __('Bu kampanyaya yorum yazılamıyor.')]);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        CampaignComment::query()->create([
            'user_id' => $request->user()->id,
            'campaign_id' => $campaign->id,
            'content' => trim($data['content']),
        ]);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->withFragment('yorumlar')
            ->with('status', __('Yorumun yayında — teşekkürler!'));
    }
}
