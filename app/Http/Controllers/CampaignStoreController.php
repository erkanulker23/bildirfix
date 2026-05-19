<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CampaignModerationStatus;
use App\Models\Campaign;
use App\Support\CampaignDraftBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignStoreController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $user !== null && ($user->isSuperAdmin() || $user->isAdmin() || $user->hasVerifiedPhone()),
            403,
            __('Kampanya göndermek için üye olup telefonunuzu doğrulamanız gerekir.'),
        );

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
            'ends_at' => null,
            'moderation_status' => CampaignModerationStatus::Pending,
            'moderated_at' => null,
            'moderated_by_user_id' => null,
            'moderation_note' => null,
        ]);

        return redirect()
            ->route('campaigns.show', $campaign)
            ->with('status', __('Kampanya taslak olarak gönderildi. Süper yönetici onayından sonra herkese açılacak.'));
    }
}
