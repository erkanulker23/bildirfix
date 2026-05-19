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
                        <p class="mt-1 text-xs text-[var(--psc-text-muted)]">{{ __('Varsayılan:') }} {{ $defaults['title'] }}</p>
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
                <div class="psc-card__body space-y-5">
                    <h2 class="psc-card__title">{{ __('Logo ve favicon') }}</h2>

                    @include('partials.admin.upload-preview', [
                        'label' => __('Site logosu'),
                        'previewUrl' => $settings->site_logo_path ? $branding->logoUrl() : null,
                        'removeName' => 'remove_site_logo',
                        'removeLabel' => __('Logoyu kaldır'),
                        'inputName' => 'site_logo',
                        'accept' => 'image/png,image/jpeg,image/webp',
                        'hint' => __('PNG, JPG veya WebP — önerilen yükseklik ~48px'),
                    ])

                    @include('partials.admin.upload-preview', [
                        'label' => __('Favicon'),
                        'previewUrl' => $settings->favicon_path ? $branding->faviconUrl() : null,
                        'previewClass' => 'h-10 w-10 object-contain',
                        'removeName' => 'remove_favicon',
                        'removeLabel' => __('Favicon’u kaldır (varsayılan SVG)'),
                        'inputName' => 'favicon',
                        'accept' => '.ico,image/png,image/svg+xml,image/webp',
                    ])

                    <div class="border-t border-[var(--psc-border-light)] pt-4">
                        @include('partials.admin.upload-preview', [
                            'label' => __('Anasayfa paylaşım görseli (Open Graph)'),
                            'previewUrl' => $settings->homepage_og_image_path ? $branding->homepageOgImageUrl() : null,
                            'previewClass' => 'max-h-32 rounded-lg object-cover',
                            'removeName' => 'remove_homepage_og_image',
                            'removeLabel' => __('OG görselini kaldır'),
                            'inputName' => 'homepage_og_image',
                            'accept' => 'image/png,image/jpeg,image/webp',
                        ])
                    </div>
                </div>
            </div>

            <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
