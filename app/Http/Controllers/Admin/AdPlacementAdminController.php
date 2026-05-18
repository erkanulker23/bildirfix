<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AdPlacementAdminController extends Controller
{
    public function index(): View
    {
        $placements = AdPlacement::query()->orderBy('sort_order')->orderBy('label')->get();

        return view('admin.ads.index', [
            'placements' => $placements,
            'adsenseClient' => config('adsense.client'),
            'adsenseEnabled' => (bool) config('adsense.enabled'),
        ]);
    }

    public function edit(AdPlacement $ad): View
    {
        return view('admin.ads.edit', ['placement' => $ad]);
    }

    public function update(Request $request, AdPlacement $ad): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['adsense', 'image', 'video'])],
            'is_active' => ['required', 'boolean'],
            'adsense_slot' => ['nullable', 'string', 'max:64'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'media' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm', 'max:51200'],
            'media_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $payload = [
            'label' => $data['label'],
            'type' => $data['type'],
            'is_active' => (bool) $data['is_active'],
            'adsense_slot' => $data['adsense_slot'] ?: null,
            'link_url' => $data['link_url'] ?: null,
        ];

        if ($request->hasFile('media')) {
            $ext = $request->file('media')->getClientOriginalExtension() ?: 'bin';
            $path = $request->file('media')->storeAs(
                'ads',
                $ad->key.'-'.time().'.'.$ext,
                'public'
            );
            $payload['media_url'] = Storage::disk('public')->url($path);
            if (str_starts_with((string) $request->file('media')->getMimeType(), 'video/')) {
                $payload['type'] = 'video';
            } elseif ($payload['type'] === 'adsense') {
                $payload['type'] = 'image';
            }
        } elseif (! empty($data['media_url'])) {
            $payload['media_url'] = $data['media_url'];
        }

        $ad->update($payload);
        AdPlacement::flushCache($ad->key);

        return redirect()
            ->route('admin.ads.edit', $ad)
            ->with('status', __('Reklam alanı güncellendi.'));
    }
}
