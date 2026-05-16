<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use App\Models\Post;
use App\Services\ComplaintMediaService;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuickComplaintController extends Controller
{
    public function store(Request $request, ComplaintMediaService $mediaService): RedirectResponse
    {
        $data = $request->validate(ComplaintDraftSession::validationRules($request));

        $user = $request->user();

        $needsDraftMedia = $user === null
            || (! $user->isSuperAdmin() && ! $user->isAdmin() && ! $user->hasVerifiedPhone());

        $draftMedia = $needsDraftMedia ? $mediaService->storeDraftUploads($request) : [];

        if ($user === null) {
            ComplaintDraftSession::put($request, array_merge($data, [
                'draft_media' => $draftMedia,
            ]));

            return redirect()
                ->route('register')
                ->with('status', __('Form dolduruldu. Son adım: üye ol ve telefonunu doğrula — bildirimin otomatik gönderilir.'));
        }

        if (! $user->isSuperAdmin() && ! $user->isAdmin() && ! $user->hasVerifiedPhone()) {
            ComplaintDraftSession::put($request, array_merge($data, [
                'draft_media' => $draftMedia,
            ]));

            return redirect()
                ->route('verify.phone.form')
                ->with('status', __('Telefon doğrulamasından sonra bildirimin otomatik kaydedilecek.'));
        }

        $districtId = (int) $data['district_id'];
        $nbTid = (int) $data['neighborhood_turkiye_id'];
        $nbName = trim((string) $data['neighborhood_name']);

        $neighborhood = Neighborhood::query()->firstOrCreate(
            [
                'district_id' => $districtId,
                'turkiye_id' => $nbTid,
            ],
            [
                'name' => $nbName,
                'slug' => Str::slug(Str::limit($nbName, 72, '').'-'.$nbTid),
            ],
        );

        $institutionIds = ComplaintDraftSession::normalizeInstitutionIds($data);

        $lat = isset($data['latitude']) && $data['latitude'] !== '' && $data['latitude'] !== null
            ? round((float) $data['latitude'], 7)
            : null;
        $lng = isset($data['longitude']) && $data['longitude'] !== '' && $data['longitude'] !== null
            ? round((float) $data['longitude'], 7)
            : null;

        $post = Post::query()->create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'media_url' => $data['media_url'] ?? null,
            'media' => [],
            'type' => 'complaint',
            'city_id' => $data['city_id'] ?? null,
            'district_id' => $districtId,
            'neighborhood_id' => $neighborhood->id,
            'latitude' => $lat,
            'longitude' => $lng,
            'category_id' => $data['category_id'] ?? null,
            'institution_id' => $institutionIds[0] ?? null,
            'status' => PostStatus::Open,
            'moderation_status' => PostModerationStatus::Pending,
            'moderated_at' => null,
            'moderated_by_user_id' => null,
            'moderation_note' => null,
        ]);

        $post->syncTargetInstitutions($institutionIds);

        $mediaItems = $mediaService->storeAuthenticatedUploads($request, $post);

        if (! empty($data['media_url'])) {
            $u = (string) $data['media_url'];
            $isVideo = str_contains(strtolower($u), 'youtube') || str_contains(strtolower($u), 'youtu.be') || str_contains(strtolower($u), 'vimeo');
            $mediaItems[] = ['type' => $isVideo ? 'video' : 'image', 'url' => $u];
        }

        if ($mediaItems !== []) {
            $first = $mediaItems[0];
            $post->forceFill([
                'media' => array_values($mediaItems),
                'media_url' => $first['url'] ?? $post->media_url,
            ])->save();
        }

        return redirect()->route('home')->with('status', __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]));
    }
}
