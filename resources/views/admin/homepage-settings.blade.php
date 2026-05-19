@extends('layouts.admin')

@section('title', __('Anasayfa ve marka'))

@section('content')
    <div class="max-w-3xl">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Anasayfa ve marka') }}</h1>
                <p class="psc-page-desc">{{ __('SEO başlık/açıklama, site logosu ve favicon. Boş bırakırsanız varsayılanlar kullanılır.') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.homepage-settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="psc-card">
                <div class="psc-card__body space-y-4">
                    <h2 class="psc-card__title">{{ __('Anasayfa SEO') }}</h2>
                    <div>
                        <label class="psc-field__label">{{ __('Sayfa başlığı (title)') }}</label>
                        <input name="homepage_seo_title" type="text" maxlength="120"
                            value="{{ old('homepage_seo_title', $settings->homepage_seo_title) }}"
                            placeholder="{{ $defaults['title'] }}"
                            class="psc-input mt-2">
                        <p class="mt-1 text-xs text-slate-500">{{ __('Varsayılan:') }} {{ $defaults['title'] }}</p>
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Meta açıklama (description)') }}</label>
                        <textarea name="homepage_seo_description" rows="3" maxlength="500"
                            class="psc-input mt-2 !h-auto py-3"
                            placeholder="{{ Str::limit($defaults['description'], 120) }}">{{ old('homepage_seo_description', $settings->homepage_seo_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-4">
                    <h2 class="psc-card__title">{{ __('Logo ve favicon') }}</h2>
                    <div class="flex flex-wrap items-center gap-4 rounded-lg border border-slate-100 bg-slate-50 p-4">
                        @if ($branding->hasCustomLogo())
                            <img src="{{ $branding->logoUrl() }}" alt="" class="h-12 max-w-[180px] object-contain">
                        @else
                            <p class="text-sm text-slate-600">{{ __('Özel logo yok — varsayılan ikon + site adı gösterilir.') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Site logosu (PNG, JPG, WebP)') }}</label>
                        <input name="site_logo" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 w-full text-sm">
                        @if ($settings->site_logo_path)
                            <label class="mt-2 flex items-center gap-2 text-sm">
                                <input type="checkbox" name="remove_site_logo" value="1"> {{ __('Logoyu kaldır') }}
                            </label>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <img src="{{ $branding->faviconUrl() }}" alt="" width="32" height="32" class="h-8 w-8 rounded border object-contain">
                        <div class="flex-1">
                            <label class="psc-field__label">{{ __('Favicon (ICO, PNG, SVG, WebP)') }}</label>
                            <input name="favicon" type="file" accept=".ico,image/png,image/svg+xml,image/webp" class="mt-2 w-full text-sm">
                            @if ($settings->favicon_path)
                                <label class="mt-2 flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="remove_favicon" value="1"> {{ __('Favicon’u kaldır (varsayılan SVG kullanılır)') }}
                                </label>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="psc-field__label">{{ __('Anasayfa paylaşım görseli (Open Graph, isteğe bağlı)') }}</label>
                        <input name="homepage_og_image" type="file" accept="image/png,image/jpeg,image/webp" class="mt-2 w-full text-sm">
                        @if ($branding->homepageOgImageUrl())
                            <img src="{{ $branding->homepageOgImageUrl() }}" alt="" class="mt-3 max-h-32 rounded-lg border object-cover">
                        @endif
                        @if ($settings->homepage_og_image_path)
                            <label class="mt-2 flex items-center gap-2 text-sm">
                                <input type="checkbox" name="remove_homepage_og_image" value="1"> {{ __('OG görselini kaldır') }}
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
