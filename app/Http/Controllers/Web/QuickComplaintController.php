<?php

namespace App\Http\Controllers\Web;

use App\Enums\PostModerationStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Institution;
use App\Models\Post;
use App\Support\ComplaintDraftSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuickComplaintController extends Controller
{
    public function create(Request $request): View
    {
        $draft = ComplaintDraftSession::get($request) ?? [];

        $cityId = $request->integer('city_id')
            ?: (isset($draft['city_id']) ? (int) $draft['city_id'] : null)
            ?: City::query()->where('plate', 34)->value('id');

        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);

        $cities = City::query()->orderBy('name')->get(['id', 'name', 'plate']);

        $institutions = Institution::query()
            ->when(
                $cityId,
                fn ($q) => $q->where(function ($sq) use ($cityId): void {
                    $sq->where('city_id', $cityId)->orWhereNull('city_id');
                }),
            )
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('complaints.quick', [
            'categories' => $categories,
            'institutions' => $institutions,
            'cityId' => $cityId,
            'cities' => $cities,
            'complaintDraft' => $draft,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:8000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'media_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user === null) {
            ComplaintDraftSession::put($request, $data);

            return redirect()
                ->route('register')
                ->with('status', __('Form dolduruldu. Son adım: üye ol ve telefonunu doğrula — bildirimin otomatik gönderilir.'));
        }

        $staff = $user->isSuperAdmin() || $user->isAdmin();
        if (! $staff && ! $user->hasVerifiedPhone()) {
            ComplaintDraftSession::put($request, $data);

            return redirect()
                ->route('verify.phone.form')
                ->with('status', __('Telefon doğrulamasından sonra bildirimin otomatik kaydedilecek.'));
        }

        $post = Post::query()->create([
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

        return redirect()->route('home')->with('status', __('Şikâyet kaydedildi (#:id). Onay sonrası herkese açılacak.', ['id' => $post->id]));
    }
}
