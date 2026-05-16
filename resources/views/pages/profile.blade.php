@extends('layouts.app')

@section('title', __('Profil').' • '.config('app.name'))

@section('content')
        <div class="mb-4 flex items-center gap-3">
            <a href="{{ route('home') }}"
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm transition hover:bg-neutral-50">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                <span class="sr-only">{{ __('Geri') }}</span>
            </a>
            <h1 class="font-heading min-w-0 truncate text-base font-bold text-neutral-900 sm:text-lg">{{ __('Profil') }}</h1>
        </div>
        <div class="mx-auto max-w-lg px-0 pt-1 pb-10 sm:px-2">
            <section class="card-post px-4 py-5">
                @php
                    $pn = trim((string) ($user->name ?? '?'));
                    preg_match_all('/\p{L}/u', $pn, $pm);
                    $pi = (($pm[0] ?? []) !== [])
                        ? mb_strtoupper(implode('', array_slice($pm[0], 0, 2)))
                        : mb_strtoupper(mb_substr($pn, 0, 2));
                    $pi = mb_substr($pi ?: '?', 0, 2);
                @endphp
                <div class="flex items-center gap-4">
                    <span
                        class="font-heading flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-secondary-soft text-xl font-bold text-white ring-4 ring-gray-100">{{ $pi }}</span>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-heading truncate text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('panel.dashboard') }}"
                                class="btn-secondary inline-flex min-h-11 items-center px-4 py-2 text-sm">{{ __('Panel') }}</a>
                            <a href="{{ route('posts.create') }}"
                                class="btn-primary inline-flex min-h-11 items-center px-4 py-2 text-sm">{{ __('Bildir') }}</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-6">
                <div class="mb-3 flex items-center justify-between px-1">
                    <h3 class="font-heading text-base font-bold text-gray-900">{{ __('Paylaşımlarım') }}</h3>
                    @if ($posts->total() > 0)
                        <span class="text-xs font-semibold text-gray-400">{{ $posts->total() }}</span>
                    @endif
                </div>

                @forelse ($posts as $post)
                    <article class="card-post mb-3 px-4 py-4">
                        <a href="{{ route('posts.show', $post) }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="line-clamp-2 font-heading text-base font-bold text-gray-900">{{ $post->title }}</h4>
                                    <p class="mt-2 flex flex-wrap gap-x-2 gap-y-1 text-xs text-gray-400">
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                        @if ($post->city)
                                            <span aria-hidden="true">•</span>
                                            <span>{{ $post->city->name }}</span>
                                        @endif
                                        @if ($post->category)
                                            <span aria-hidden="true">•</span>
                                            <span>{{ $post->category->name }}</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="badge shrink-0 text-[11px]">{{ $post->status->label() }}</span>
                            </div>
                        </a>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-14 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </div>
                        <p class="font-heading text-base font-bold text-gray-900">{{ __('Henüz paylaşım yok') }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ __('İlk bildirini oluşturarak başlayabilirsin.') }}</p>
                        <a href="{{ route('posts.create') }}" class="btn-primary mt-6 inline-flex min-h-11 items-center px-6">{{ __('Paylaşım oluştur') }}</a>
                    </div>
                @endforelse

                @if ($posts->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </section>
        </div>
@endsection
