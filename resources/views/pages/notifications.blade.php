@extends('layouts.app')

@section('title', __('Bildirimler').' • '.config('app.name'))

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
            <h1 class="font-heading min-w-0 truncate text-base font-bold text-neutral-900 sm:text-lg">{{ __('Bildirimler') }}</h1>
        </div>
        <div class="mx-auto max-w-lg px-0 pt-1 pb-10 sm:px-2">
            @forelse ($notifications as $notification)
                @php
                    $payload = is_array($notification->data) ? $notification->data : [];
                    $heading = $payload['title'] ?? $payload['message'] ?? class_basename($notification->type);
                    $body = $payload['body'] ?? $payload['content'] ?? $payload['url'] ?? null;
                @endphp
                <article
                    class="mb-3 rounded-2xl border px-4 py-4 [transition:var(--transition-base)] {{ $notification->read_at ? 'border-gray-100 bg-white' : 'border-primary/20 bg-primary-light/40' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900">{{ $heading }}</p>
                            @if (is_string($body) && $body !== '')
                                <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ Str::limit($body, 220) }}</p>
                            @endif
                            <time class="mt-2 block text-xs font-medium text-gray-400"
                                datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                        </div>
                        @if (! $notification->read_at)
                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-primary" title="{{ __('Okunmadı') }}"></span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                        </svg>
                    </div>
                    <p class="font-heading text-base font-bold text-gray-900">{{ __('Bildirim yok') }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Yeni gelişmeler burada görünecek.') }}</p>
                </div>
            @endforelse

            @if ($notifications->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
@endsection
