<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use App\Models\Post;
use App\Models\User;
use App\Services\ComplaintMediaService;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuickComplaintController extends Controller
{
    /**
     * Üye olmayan veya telefonu doğrulanmamış kullanıcılar için yalnızca oturum taslağı.
     * Veritabanına kayıt oluşturulmaz.
     */
    public function storeDraft(Request $request, ComplaintMediaService $mediaService): RedirectResponse
    {
        $user = $request->user();

        if ($user !== null && self::canPublishComplaint($user)) {
            return redirect()
                ->route('posts.create')
                ->with('status', __('Giriş yaptınız. Bildirimi şimdi gönderebilirsiniz.'));
        }

        $data = $request->validate(ComplaintDraftSession::validationRules($request));
        $draftMedia = $mediaService->storeDraftUploads($request);

        ComplaintDraftSession::put($request, array_merge($data, [
            'draft_media' => $draftMedia,
        ]));

        if ($user === null) {
            return redirect()
                ->route('register')
                ->with('status', __('Form kaydedildi. Son adım: üye ol ve telefonunu doğrula — bildirimin otomatik gönderilir.'));
        }

        return redirect()
            ->route('verify.phone.form')
            ->with('status', __('Telefon doğrulamasından sonra bildirimin otomatik kaydedilecek.'));
    }

    /**
     * Onaylı üye + doğrulanmış telefon ile doğrudan yayın (taslak değil).
     */
    public function store(Request $request, ComplaintMediaService $mediaService): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null && self::canPublishComplaint($user), 403);

        $data = $request->validate(ComplaintDraftSession::validationRules($request));

        $post = $this->createPostFromValidated($user, $data, $mediaService, $request);

        return redirect()->route('home')->with('status', __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]));
    }

    public static function canPublishComplaint(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isAdmin() || $user->hasVerifiedPhone();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createPostFromValidated(User $user, array $data, ComplaintMediaService $mediaService, Request $request): Post
    {
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

        return $post;
    }
}
