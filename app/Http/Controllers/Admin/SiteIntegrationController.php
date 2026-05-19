<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\SiteIntegrations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteIntegrationController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformSetting::current();
        $integrations = SiteIntegrations::fromPlatform();

        if ($integrations->indexnowKey === null) {
            $settings->indexnow_key = SiteIntegrations::generateIndexNowKey();
            $settings->save();
            $integrations = SiteIntegrations::fromPlatform();
        }

        return view('admin.site-integrations', [
            'settings' => $settings,
            'integrations' => $integrations,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_site_verification' => ['nullable', 'string', 'max:128'],
            'google_analytics_measurement_id' => ['nullable', 'string', 'max:32'],
            'yandex_verification' => ['nullable', 'string', 'max:128'],
            'bing_site_verification' => ['nullable', 'string', 'max:128'],
            'custom_head_css' => ['nullable', 'string', 'max:50000'],
            'custom_head_html' => ['nullable', 'string', 'max:50000'],
            'custom_body_html' => ['nullable', 'string', 'max:50000'],
        ]);

        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();

        foreach ([
            'google_site_verification',
            'google_analytics_measurement_id',
            'yandex_verification',
            'bing_site_verification',
            'custom_head_css',
            'custom_head_html',
            'custom_body_html',
        ] as $field) {
            $value = isset($validated[$field]) ? trim((string) $validated[$field]) : '';
            $platform->{$field} = $value !== '' ? $value : null;
        }

        $ga = $platform->google_analytics_measurement_id;
        if (is_string($ga) && $ga !== '') {
            $ga = strtoupper($ga);
            if (preg_match('/^G-[A-Z0-9]+$/', $ga) !== 1) {
                return back()->withErrors([
                    'google_analytics_measurement_id' => __('Geçerli bir GA4 ölçüm kimliği girin (ör. G-XXXXXXXXXX).'),
                ])->withInput();
            }
            $platform->google_analytics_measurement_id = $ga;
        }

        if ($platform->indexnow_key === null || trim((string) $platform->indexnow_key) === '') {
            $platform->indexnow_key = SiteIntegrations::generateIndexNowKey();
        }

        $platform->save();

        return redirect()
            ->route('admin.site-integrations.edit')
            ->with('status', __('Site entegrasyonları kaydedildi.'));
    }

    public function regenerateIndexNowKey(): RedirectResponse
    {
        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();
        $platform->indexnow_key = SiteIntegrations::generateIndexNowKey();
        $platform->save();

        return redirect()
            ->route('admin.site-integrations.edit')
            ->with('status', __('IndexNow anahtarı yenilendi. Bing Webmaster’da yeni anahtar dosyasını doğrulayın.'));
    }
}
