<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PlatformSettingController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformSetting::current();
        $redirectUri = route('auth.google.callback', absolute: true);

        return view('admin.platform-settings', compact('settings', 'redirectUri'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_oauth_enabled' => ['required', 'integer', 'in:0,1'],
            'google_client_id' => ['nullable', 'string', 'max:8192'],
            'google_client_secret' => ['nullable', 'string', 'max:8192'],
        ]);

        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();

        /** @var array<string, mixed> $stored */
        $stored = PlatformSetting::query()->whereKey($platform->id)->firstOrFail()->getRawOriginal();

        $storedSecretCipherPresent = isset($stored['google_client_secret'])
            && $stored['google_client_secret'] !== null
            && $stored['google_client_secret'] !== '';

        $enabled = ((int) $validated['google_oauth_enabled']) === 1;

        $idTrim = trim((string) ($validated['google_client_id'] ?? ''));
        $secretTrim = isset($validated['google_client_secret']) ? trim((string) $validated['google_client_secret']) : '';

        if ($enabled && $idTrim === '') {
            throw ValidationException::withMessages([
                'google_client_id' => __('Google OAuth açıkken istemci kimliği gereklidir.'),
            ]);
        }

        if ($enabled && $secretTrim === '' && ! $storedSecretCipherPresent) {
            throw ValidationException::withMessages([
                'google_client_secret' => __('İlk aktivasyonda gizli anahtarı da girmeniz gerekir.'),
            ]);
        }

        $platform->google_oauth_enabled = $enabled;
        $platform->google_client_id = $idTrim !== '' ? $idTrim : null;

        if ($secretTrim !== '') {
            $platform->google_client_secret = $secretTrim;
        }

        if (! $platform->googleOAuthConfigured()) {
            $platform->google_oauth_enabled = false;
        }

        $platform->save();

        return redirect()
            ->route('admin.platform-settings.edit')
            ->with('status', __('Kaydedildi'));
    }
}
