@extends('layouts.admin')

@section('admin_heading', __('Reklamlar'))
@section('title', __('Reklam alanları'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Reklam alanları') }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                {{ __('Google AdSense veya kendi görsel/videonuz. AdSense istemci kimliği .env içindeki ADSENSE_CLIENT ile gelir.') }}
                @if ($adsenseEnabled && filled($adsenseClient))
                    <span class="font-bold text-emerald-700">{{ __('AdSense yapılandırıldı') }}</span>
                @else
                    <span class="font-bold text-amber-700">{{ __('AdSense kapalı veya eksik') }}</span>
                @endif
            </p>
        </div>

        <div class="space-y-3">
            @foreach ($placements as $p)
                <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div>
                        <p class="font-bold text-slate-900">{{ $p->label }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            <code class="rounded bg-slate-100 px-1">{{ $p->key }}</code>
                            · {{ $p->type }}
                            @if ($p->is_active)
                                <span class="text-emerald-700">{{ __('Aktif') }}</span>
                            @else
                                <span class="text-slate-400">{{ __('Pasif') }}</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.ads.edit', $p) }}"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">{{ __('Düzenle') }}</a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
