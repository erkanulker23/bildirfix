@extends('layouts.admin')

@section('admin_heading', __('Yasal sayfalar'))
@section('title', __('Yasal sayfalar'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Yasal metinler') }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ __('Gizlilik, KVKK ve kullanım koşulları sayfalarını düzenleyin. HTML kullanabilirsiniz (h1, h2, p, ul, a). Boş bırakırsanız varsayılan taslak gösterilir.') }}</p>
        </div>

        <form action="{{ route('admin.legal-pages.update') }}" method="post" class="space-y-8">
            @csrf
            @method('PATCH')

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-900">{{ __('Gizlilik politikası') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ route('legal.privacy') }}</p>
                <textarea name="legal_privacy_html" rows="14"
                    class="mt-4 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-[13px] leading-relaxed text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="{{ __('Boş bırakılırsa varsayılan taslak kullanılır.') }}">{{ old('legal_privacy_html', $settings->legal_privacy_html) }}</textarea>
                @error('legal_privacy_html')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                <details class="mt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-slate-500">{{ __('Varsayılan taslağı göster') }}</summary>
                    <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-slate-100 p-3 text-[11px] text-slate-700">{{ $defaults['privacy'] }}</pre>
                </details>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-900">{{ __('KVKK') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ route('legal.kvkk') }}</p>
                <textarea name="legal_kvkk_html" rows="14"
                    class="mt-4 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-[13px] leading-relaxed text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="{{ __('Boş bırakılırsa varsayılan taslak kullanılır.') }}">{{ old('legal_kvkk_html', $settings->legal_kvkk_html) }}</textarea>
                @error('legal_kvkk_html')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                <details class="mt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-slate-500">{{ __('Varsayılan taslağı göster') }}</summary>
                    <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-slate-100 p-3 text-[11px] text-slate-700">{{ $defaults['kvkk'] }}</pre>
                </details>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-900">{{ __('Kullanım koşulları') }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ route('legal.terms') }}</p>
                <textarea name="legal_terms_html" rows="14"
                    class="mt-4 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-[13px] leading-relaxed text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    placeholder="{{ __('Boş bırakılırsa varsayılan taslak kullanılır.') }}">{{ old('legal_terms_html', $settings->legal_terms_html) }}</textarea>
                @error('legal_terms_html')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                <details class="mt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-slate-500">{{ __('Varsayılan taslağı göster') }}</summary>
                    <pre class="mt-2 max-h-40 overflow-auto rounded-lg bg-slate-100 p-3 text-[11px] text-slate-700">{{ $defaults['terms'] }}</pre>
                </details>
            </section>

            <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
