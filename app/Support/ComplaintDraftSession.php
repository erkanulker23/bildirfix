<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'description' => ['nullable', 'string', 'max:8000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'media_url' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($validator->fails()) {
            self::forget($request);

            return null;
        }

        $data = $validator->validated();

        self::forget($request);

        return Post::query()->create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'media_url' => $data['media_url'] ?? null,
            'media' => [],
            'type' => 'complaint',
            'city_id' => $data['city_id'] ?? null,
            'district_id' => null,
            'neighborhood_id' => null,
            'latitude' => null,
            'longitude' => null,
            'category_id' => $data['category_id'] ?? null,
            'institution_id' => $data['institution_id'] ?? null,
            'status' => PostStatus::Open,
            'moderation_status' => PostModerationStatus::Pending,
            'moderated_at' => null,
            'moderated_by_user_id' => null,
            'moderation_note' => null,
        ]);
    }
}
