<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\LegalContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LegalPagesController extends Controller
{
    public function edit(): View
    {
        $settings = PlatformSetting::current();

        return view('admin.legal-pages', [
            'settings' => $settings,
            'defaults' => [
                'privacy' => LegalContent::defaultHtml(LegalContent::PRIVACY),
                'kvkk' => LegalContent::defaultHtml(LegalContent::KVKK),
                'terms' => LegalContent::defaultHtml(LegalContent::TERMS),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'legal_privacy_html' => ['nullable', 'string', 'max:200000'],
            'legal_kvkk_html' => ['nullable', 'string', 'max:200000'],
            'legal_terms_html' => ['nullable', 'string', 'max:200000'],
        ]);

        /** @var PlatformSetting $platform */
        $platform = PlatformSetting::current();

        $platform->legal_privacy_html = $this->nullableTrim($validated['legal_privacy_html'] ?? null);
        $platform->legal_kvkk_html = $this->nullableTrim($validated['legal_kvkk_html'] ?? null);
        $platform->legal_terms_html = $this->nullableTrim($validated['legal_terms_html'] ?? null);
        $platform->save();

        return redirect()
            ->route('admin.legal-pages.edit')
            ->with('status', __('Yasal sayfalar güncellendi.'));
    }

    private function nullableTrim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
