@extends('layouts.app')

@section('toolbar')
    <div></div>
@endsection

@section('title', __('Kampanya moderasyonu'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">{{ __('Onay bekleyen kampanyalar') }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ __('Süper yönetici olarak sosyal sorumluluk kampanyalarını yayına alırsınız.') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">←
                {{ __('Yönetim') }}</a>
            <a href="{{ route('admin.moderation.index') }}"
                class="rounded-lg bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-900 hover:bg-indigo-200">{{ __('Şikâyet moderasyonu') }}</a>
        </div>
    </div>

    @if ($campaigns->isEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-600 shadow-sm">
            {{ __('Bekleyen kampanya yok.') }}
        </section>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <ul class="divide-y divide-slate-100">
                @foreach ($campaigns as $campaign)
                    <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">{{ __('SSR • Bekliyor') }}</p>
                            <p class="mt-1 text-base font-semibold text-slate-900">{{ $campaign->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $campaign->user?->name }}
                                · {{ $campaign->city?->name ?? __('Genel') }}</p>
                            @if ($campaign->excerpt)
                                <p class="mt-2 line-clamp-3 text-sm text-slate-600">{{ $campaign->excerpt }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2 sm:flex-col">
                            <form method="POST" action="{{ route('admin.campaign-moderation.approve', $campaign) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                    {{ __('Yayına al') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.campaign-moderation.reject', $campaign) }}"
                                class="space-y-1">
                                @csrf
                                <input type="text" name="moderation_note" maxlength="2000"
                                    placeholder="{{ __('Red gerekçesi (isteğe bağlı)') }}"
                                    class="mb-1 w-full min-w-[12rem] rounded-lg border border-slate-300 px-3 py-1.5 text-xs">
                                <button type="submit"
                                    class="w-full rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500">
                                    {{ __('Reddet') }}
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="border-t border-slate-100 px-4 py-3">{{ $campaigns->links() }}</div>
        </div>
    @endif
@endsection
