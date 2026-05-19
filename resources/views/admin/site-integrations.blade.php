@extends('layouts.admin')

@section('title', __('SEO ve site entegrasyonları'))

@section('content')
    <div class="max-w-3xl">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('SEO, analitik ve doğrulama') }}</h1>
                <p class="psc-page-desc">
                    {{ __('Google Search Console, Analytics, Yandex ve Bing doğrulama kodları; IndexNow anahtarı; ek CSS ve script alanları.') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.site-integrations.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Google Search Console') }}</h2>
                    <p class="psc-card__sub">{{ __('HTML etiketi yöntemi → meta içindeki content değerini yapıştırın.') }}</p>
                    <div>
                        <label class="psc-field__label">{{ __('Doğrulama kodu (content)') }}</label>
                        <input name="google_site_verification" type="text"
                            value="{{ old('google_site_verification', $settings->google_site_verification) }}"
                            placeholder="ör. abcd1234efgh5678..."
                            class="psc-input mt-2 font-mono text-sm">
                        @error('google_site_verification')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Google Analytics (GA4)') }}</h2>
                    <p class="psc-card__sub">{{ __('Ölçüm kimliği (Measurement ID). Örnek: G-XXXXXXXXXX') }}</p>
                    <div>
                        <label class="psc-field__label">{{ __('Measurement ID') }}</label>
                        <input name="google_analytics_measurement_id" type="text"
                            value="{{ old('google_analytics_measurement_id', $settings->google_analytics_measurement_id) }}"
                            placeholder="G-XXXXXXXXXX"
                            class="psc-input mt-2 font-mono text-sm">
                        @error('google_analytics_measurement_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Yandex Webmaster') }}</h2>
                    <div>
                        <label class="psc-field__label">{{ __('Meta doğrulama (content)') }}</label>
                        <input name="yandex_verification" type="text"
                            value="{{ old('yandex_verification', $settings->yandex_verification) }}"
                            class="psc-input mt-2 font-mono text-sm">
                        @error('yandex_verification')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Bing Webmaster') }}</h2>
                    <div>
                        <label class="psc-field__label">{{ __('msvalidate.01 (content)') }}</label>
                        <input name="bing_site_verification" type="text"
                            value="{{ old('bing_site_verification', $settings->bing_site_verification) }}"
                            class="psc-input mt-2 font-mono text-sm">
                        @error('bing_site_verification')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>


            <div class="psc-card">
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Ek stiller ve scriptler') }}</h2>
                    <p class="psc-card__sub">{{ __('Yalnızca güvendiğiniz kodları ekleyin. Yönetim paneline uygulanmaz; yalnızca herkese açık site.') }}</p>
                    <div>
                        <label class="psc-field__label">{{ __('Özel CSS (&lt;style&gt;)') }}</label>
                        <textarea name="custom_head_css" rows="6" class="psc-input mt-2 !h-auto py-3 font-mono text-xs"
                            placeholder=".my-banner { display: none; }">{{ old('custom_head_css', $settings->custom_head_css) }}</textarea>
                        @error('custom_head_css')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Head HTML (meta, script, doğrulama etiketleri)') }}</label>
                        <textarea name="custom_head_html" rows="8" class="psc-input mt-2 !h-auto py-3 font-mono text-xs"
                            placeholder="&lt;meta name=&quot;...&quot; content=&quot;...&quot;&gt;">{{ old('custom_head_html', $settings->custom_head_html) }}</textarea>
                        @error('custom_head_html')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Body sonu HTML (isteğe bağlı)') }}</label>
                        <textarea name="custom_body_html" rows="6" class="psc-input mt-2 !h-auto py-3 font-mono text-xs">{{ old('custom_body_html', $settings->custom_body_html) }}</textarea>
                        @error('custom_body_html')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="psc-btn psc-btn--primary w-full sm:w-auto">{{ __('Kaydet') }}</button>
        </form>

        <div class="psc-card mt-6">
            <div class="psc-card__body space-y-4">
                <h2 class="psc-card__title">{{ __('Bing IndexNow') }}</h2>
                <p class="psc-card__sub">
                    {{ __('Anahtar dosyası kök dizinde yayınlanır. Bing Webmaster → URL Gönderimi → IndexNow bölümünde anahtar dosyası URL’sini doğrulayın.') }}
                </p>
                @if ($integrations->indexNowKeyFileUrl())
                    <div class="rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm">
                        <p class="font-semibold text-orange-950">{{ __('Anahtar dosyası URL') }}</p>
                        <p class="mt-2 break-all font-mono text-xs text-orange-900">{{ $integrations->indexNowKeyFileUrl() }}</p>
                        <p class="mt-2 text-xs text-orange-800">{{ __('Dosya içeriği yalnızca anahtar metnidir (tek satır).') }}</p>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.site-integrations.regenerate-indexnow') }}"
                    onsubmit="return confirm({{ json_encode(__('IndexNow anahtarını yenilemek Bing doğrulamasını sıfırlar. Devam?')) }});">
                    @csrf
                    <button type="submit" class="psc-btn psc-btn--secondary">{{ __('Anahtarı yenile') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
