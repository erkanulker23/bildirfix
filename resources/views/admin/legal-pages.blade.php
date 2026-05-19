@extends('layouts.admin')

@section('title', __('Yasal sayfalar'))

@section('content')
    @include('partials.admin.quill-assets')

    <div class="max-w-4xl space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Yasal metinler') }}</h1>
                <p class="psc-page-desc">{{ __('Gizlilik, KVKK ve kullanım koşulları. Boş bırakırsanız varsayılan taslak gösterilir.') }}</p>
            </div>
        </div>

        <form action="{{ route('admin.legal-pages.update') }}" method="post" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="psc-card">
                <div class="psc-card__body space-y-4">
                    <p class="text-xs text-[var(--psc-text-muted)]">{{ route('legal.privacy') }}</p>
                    @include('partials.admin.quill-field', [
                        'name' => 'legal_privacy_html',
                        'label' => __('Gizlilik politikası'),
                        'value' => old('legal_privacy_html', $settings->legal_privacy_html),
                        'editorId' => 'legal-privacy-editor',
                    ])
                    <details>
                        <summary class="cursor-pointer text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Varsayılan taslağı göster') }}</summary>
                        <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-[var(--psc-bg)] p-3 text-[11px]">{{ $defaults['privacy'] }}</pre>
                    </details>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-4">
                    <p class="text-xs text-[var(--psc-text-muted)]">{{ route('legal.kvkk') }}</p>
                    @include('partials.admin.quill-field', [
                        'name' => 'legal_kvkk_html',
                        'label' => __('KVKK'),
                        'value' => old('legal_kvkk_html', $settings->legal_kvkk_html),
                        'editorId' => 'legal-kvkk-editor',
                    ])
                    <details>
                        <summary class="cursor-pointer text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Varsayılan taslağı göster') }}</summary>
                        <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-[var(--psc-bg)] p-3 text-[11px]">{{ $defaults['kvkk'] }}</pre>
                    </details>
                </div>
            </div>

            <div class="psc-card">
                <div class="psc-card__body space-y-4">
                    <p class="text-xs text-[var(--psc-text-muted)]">{{ route('legal.terms') }}</p>
                    @include('partials.admin.quill-field', [
                        'name' => 'legal_terms_html',
                        'label' => __('Kullanım koşulları'),
                        'value' => old('legal_terms_html', $settings->legal_terms_html),
                        'editorId' => 'legal-terms-editor',
                    ])
                    <details>
                        <summary class="cursor-pointer text-xs font-semibold text-[var(--psc-text-muted)]">{{ __('Varsayılan taslağı göster') }}</summary>
                        <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-[var(--psc-bg)] p-3 text-[11px]">{{ $defaults['terms'] }}</pre>
                    </details>
                </div>
            </div>

            <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
