<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Models\Neighborhood;
use App\Models\Post;
use App\Models\User;
use App\Services\ComplaintMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class ComplaintDraftSession
{
    public const SESSION_KEY = 'complaint_draft';

    /** @param  array<string, mixed>  $validated */
    public static function put(Request $request, array $validated): void
    {
        $request->session()->put(self::SESSION_KEY, $validated);
    }

    public static function has(Request $request): bool
    {
        return $request->session()->has(self::SESSION_KEY);
    }

    /** @return array<string, mixed>|null */
    public static function get(Request $request): ?array
    {
        $raw = $request->session()->get(self::SESSION_KEY);

        return is_array($raw) ? $raw : null;
    }

    public static function forget(Request $request): void
    {
        $request->session()->forget(self::SESSION_KEY);
    }

    public static function createPostIfAny(Request $request, User $user): ?Post
    {
        $draft = self::get($request);
        if ($draft === null) {
            return null;
        }

        $staffSkipsPhone = $user->isSuperAdmin() || $user->isAdmin();
        if (! $staffSkipsPhone && ! $user->hasVerifiedPhone()) {
            return null;
        }

        $validator = Validator::make($draft, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:8000'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'neighborhood_turkiye_id' => ['required', 'integer'],
            'neighborhood_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'institution_ids' => ['nullable', 'array', 'max:20'],
            'institution_ids.*' => ['integer', 'exists:institutions,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'media_url' => ['nullable', 'url', 'max:2048'],
            'draft_media' => ['nullable', 'array'],
            'draft_media.*.path' => ['required_with:draft_media', 'string'],
            'draft_media.*.type' => ['required_with:draft_media', 'in:image,video'],
        ]);

        if ($validator->fails()) {
            self::forget($request);

            return null;
        }

        $data = $validator->validated();
        $institutionIds = self::normalizeInstitutionIds($data);

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

        self::forget($request);

        $mediaService = app(ComplaintMediaService::class);

        $mediaItems = [];
        $draftMedia = isset($data['draft_media']) && is_array($data['draft_media']) ? $data['draft_media'] : [];

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

        if ($draftMedia !== []) {
            $mediaItems = array_merge($mediaItems, $mediaService->promoteDraftToPost($post, $draftMedia));
        }

        if (! empty($data['media_url'])) {
            $u = (string) $data['media_url'];
            $low = strtolower($u);
            $isVideo = str_contains($low, 'youtube') || str_contains($low, 'youtu.be') || str_contains($low, 'vimeo');
            $mediaItems[] = ['type' => $isVideo ? 'video' : 'image', 'url' => $u];
        }

        if ($mediaItems !== []) {
            $first = $mediaItems[0];
            $post->forceFill([
                'media' => array_values($mediaItems),
                'media_url' => $first['url'] ?? $post->media_url,
            ])->save();
        }

        $post->syncTargetInstitutions($institutionIds);

        return $post;
    }

    /**
     * Tekil `institution_id` ile çoklu `institution_ids[]` girdilerini birleştirir.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, int>
     */
    public static function normalizeInstitutionIds(array $data): array
    {
        $ids = [];
        if (! empty($data['institution_ids']) && is_array($data['institution_ids'])) {
            foreach ($data['institution_ids'] as $id) {
                if ($id === null || $id === '') {
                    continue;
                }
                $ids[] = (int) $id;
            }
        }
        if (! empty($data['institution_id'])) {
            $ids[] = (int) $data['institution_id'];
        }

        return array_values(array_unique(array_filter($ids, static fn (int $id) => $id > 0)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationRules(Request $request): array
    {
        $maxImg = (int) config('complaint.max_images', 5);
        $maxVid = (int) config('complaint.max_videos', 2);
        $imgKb = (int) config('complaint.image_max_kb', 6144);
        $vidKb = (int) config('complaint.video_max_kb', 35840);

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:8000'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => [
                'required',
                'integer',
                Rule::exists('districts', 'id')->where(function ($q) use ($request): void {
                    $q->where('city_id', (int) $request->input('city_id'))
                        ->whereNotNull('turkiye_id');
                }),
            ],
            'neighborhood_turkiye_id' => ['required', 'integer', 'min:1'],
            'neighborhood_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'institution_ids' => ['nullable', 'array', 'max:20'],
            'institution_ids.*' => ['integer', 'exists:institutions,id'],
            'media_url' => ['nullable', 'url', 'max:2048'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['nullable', 'array', 'max:'.$maxImg],
            'images.*' => ['image', 'mimes:jpeg,png,gif,webp', 'max:'.($imgKb * 1024)],
            'videos' => ['nullable', 'array', 'max:'.$maxVid],
            'videos.*' => ['file', 'mimes:mp4,webm', 'max:'.($vidKb * 1024)],
        ];
    }
}
