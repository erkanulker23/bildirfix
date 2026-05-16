@extends('layouts.app')

@section('title', __('Moderasyon'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-slate-900">{{ __('Onay bekleyen şikâyetler') }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ __('Yalnızca süper yönetici yayına alır veya reddeder.') }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="{{ route('admin.campaign-moderation.index') }}"
                    class="inline-flex rounded-lg bg-violet-700 px-3 py-2 text-[12px] font-semibold text-white hover:bg-violet-600">{{ __('Kampanya moderasyonu') }}</a>
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}"
            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">
            ← {{ __('Yönetim özeti') }}
        </a>
    </div>

    @if ($posts->isEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-600 shadow-sm">
            {{ __('Bekleyen şikâyet yok.') }}
        </section>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <ul class="divide-y divide-slate-100">
                @foreach ($posts as $post)
                    <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">{{ __('Bekliyor') }}</p>
                            <p class="mt-1 truncate text-base font-semibold text-slate-900">{{ $post->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $post->user?->name }} · {{ $post->city?->name }}</p>
                            @if ($post->description)
                                <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $post->description }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2 sm:flex-col">
                            <form method="POST" action="{{ route('admin.moderation.approve', $post) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                    {{ __('Yayına al') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.moderation.reject', $post) }}" class="space-y-1">
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
            <div class="border-t border-slate-100 px-4 py-3">{{ $posts->links() }}</div>
        </div>
    @endif
@endsection
