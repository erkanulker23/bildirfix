<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\SiteBranding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformSetting::current();
        $branding = SiteBranding::fromPlatform();

        return view('admin.homepage-settings', [
            'settings' => $settings,
            'branding' => $branding,
            'defaults' => [
                'title' => SiteBranding::defaultHomepageTitle(),
                'description' => SiteBranding::defaultHomepageDescription(),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'homepage_seo_title' => ['nullable', 'string', 'max:120'],
            'homepage_seo_description' => ['nullable', 'string', 'max:500'],
            'site_logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,svg,webp', 'max:512'],
            'homepage_og_image' => ['nullable', 'image', 'max:3072'],
            'remove_site_logo' => ['sometimes', 'boolean'],
            'remove_favicon' => ['sometimes', 'boolean'],
            'remove_homepage_og_image' => ['sometimes', 'boolean'],
        ]);

        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();

        $platform->homepage_seo_title = $this->nullableTrim($validated['homepage_seo_title'] ?? null);
        $platform->homepage_seo_description = $this->nullableTrim($validated['homepage_seo_description'] ?? null);

        if ($request->boolean('remove_site_logo')) {
            $platform->site_logo_path = null;
        }
        if ($request->boolean('remove_favicon')) {
            $platform->favicon_path = null;
        }
        if ($request->boolean('remove_homepage_og_image')) {
            $platform->homepage_og_image_path = null;
        }

        $dir = public_path('images/site');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($request->hasFile('site_logo')) {
            $ext = $request->file('site_logo')->guessExtension() ?: 'png';
            $filename = 'logo-'.time().'.'.$ext;
            $request->file('site_logo')->move($dir, $filename);
            $platform->site_logo_path = '/images/site/'.$filename;
        }

        if ($request->hasFile('favicon')) {
            $ext = $request->file('favicon')->guessExtension() ?: 'png';
            $filename = 'favicon-'.time().'.'.$ext;
            $request->file('favicon')->move($dir, $filename);
            $platform->favicon_path = '/images/site/'.$filename;
        }

        if ($request->hasFile('homepage_og_image')) {
            $ext = $request->file('homepage_og_image')->guessExtension() ?: 'jpg';
            $filename = 'og-home-'.time().'.'.$ext;
            $request->file('homepage_og_image')->move($dir, $filename);
            $platform->homepage_og_image_path = '/images/site/'.$filename;
        }

        $platform->save();

        return redirect()
            ->route('admin.homepage-settings.edit')
            ->with('status', __('Anasayfa ve marka ayarları kaydedildi.'));
    }

    private function nullableTrim(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed !== '' ? $trimmed : null;
    }
}
