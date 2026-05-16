<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MailSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformSetting::current();

        return view('admin.mail-settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_use_custom_smtp' => ['required', 'integer', 'in:0,1'],
            'mail_from_address' => ['nullable', 'string', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'string', 'max:8'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:8192'],
        ]);

        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();

        $enabled = ((int) $validated['mail_use_custom_smtp']) === 1;

        $stored = $platform->getRawOriginal();
        $storedPasswordPresent = isset($stored['mail_password'])
            && $stored['mail_password'] !== null
            && $stored['mail_password'] !== '';

        $hostTrim = trim((string) ($validated['mail_host'] ?? ''));
        $fromTrim = trim((string) ($validated['mail_from_address'] ?? ''));
        $secretTrim = isset($validated['mail_password']) ? trim((string) $validated['mail_password']) : '';
        $enc = strtolower(trim((string) ($validated['mail_encryption'] ?? '')));
        $encNorm = in_array($enc, ['tls', 'ssl'], true) ? $enc : null;

        if ($enabled && $hostTrim === '') {
            throw ValidationException::withMessages([
                'mail_host' => __('Özel SMTP açıkken sunucu adresi gereklidir.'),
            ]);
        }

        if ($enabled && $fromTrim === '') {
            throw ValidationException::withMessages([
                'mail_from_address' => __('Gönderen e-posta adresi gereklidir.'),
            ]);
        }

        if ($enabled && $secretTrim === '' && ! $storedPasswordPresent) {
            throw ValidationException::withMessages([
                'mail_password' => __('İlk aktivasyonda SMTP şifresini de girmeniz gerekir.'),
            ]);
        }

        $platform->mail_use_custom_smtp = $enabled;
        $platform->mail_from_address = $fromTrim !== '' ? $fromTrim : null;
        $platform->mail_from_name = trim((string) ($validated['mail_from_name'] ?? '')) !== ''
            ? trim((string) $validated['mail_from_name']) : null;
        $platform->mail_host = $hostTrim !== '' ? $hostTrim : null;
        $platform->mail_port = $validated['mail_port'] ?? null;
        $platform->mail_encryption = $encNorm;
        $platform->mail_username = trim((string) ($validated['mail_username'] ?? '')) !== ''
            ? trim((string) $validated['mail_username']) : null;

        if ($secretTrim !== '') {
            $platform->mail_password = $secretTrim;
        }

        if (! $platform->customSmtpConfigured()) {
            $platform->mail_use_custom_smtp = false;
        }

        $platform->save();

        return redirect()
            ->route('admin.mail-settings.edit')
            ->with('status', __('E-posta ayarları kaydedildi.'));
    }
}
