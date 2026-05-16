<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CampaignModerationStatus;
use App\Models\Campaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CampaignStoreController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'excerpt' => ['nullable', 'string', 'max:480'],
            'description' => ['required', 'string', 'max:15000'],
            'hero_image_url' => ['nullable', 'url', 'max:2000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'goal_supporters' => ['nullable', 'integer', 'min:10', 'max:2147483646'],
            'ends_at' => ['nullable', 'date'],
        ]);

        $base = Campaign::slugFromTitle((string) $data['title']);
        $slug = Campaign::uniqueSlug($base);

        $campaign = Campaign::query()->create([
            'user_id' => (int) $request->user()->id,
            'title' => (string) $data['title'],
            'slug' => $slug,
            'excerpt' => isset($data['excerpt']) ? (string) $data['excerpt'] : null,
            'description' => (string) $data['description'],
            'hero_image_url' => isset($data['hero_image_url']) ? (string) $data['hero_image_url'] : null,
            'city_id' => isset($data['city_id']) ? (int) $data['city_id'] : null,
            'goal_supporters' => $data['goal_supporters'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
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
