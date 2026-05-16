@extends('layouts.app')

@php
    $pageTitle = __('Blog').' • '.config('app.name');
@endphp
@section('title', $pageTitle)

@section('content')
    <div class="mx-auto max-w-3xl px-1">
        <div class="mb-4 flex justify-end gap-2">
            <a href="{{ route('home') }}"
                class="rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-semibold text-neutral-800 hover:bg-neutral-50">{{ __('Akış') }}</a>
        </div>
        <header class="relative overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-teal-50/90 px-6 py-10 shadow-lg shadow-indigo-900/5 ring-1 ring-indigo-100/60 sm:px-10">
            <p class="text-[11px] font-black uppercase tracking-[0.22em] text-indigo-700">{{ __('Güncel') }}</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-neutral-900 sm:text-[2.1rem]">{{ __('Blog') }}</h1>
            <p class="mt-3 max-w-xl text-[15px] font-medium leading-relaxed text-neutral-600">{{ __('Kent, çevre ve platformla ilgili duyurular ile rehber yazılar.') }}</p>
        </header>

        <div class="mt-8 space-y-5">
            @forelse ($posts as $post)
                <article
                    class="group rounded-2xl border border-neutral-100 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] transition hover:border-indigo-100 hover:shadow-md">
                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                        class="block text-xl font-black leading-snug text-neutral-900 transition group-hover:text-indigo-800">
                        {{ $post->title }}</a>
                    @if ($post->excerpt)
                        <p class="mt-3 text-[15px] leading-relaxed text-neutral-600">{{ $post->excerpt }}</p>
                    @endif
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-[12px] font-semibold text-neutral-500">
                        @if ($post->published_at)
                            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('d F Y') }}</time>
                        @endif
                        @if ($post->author)
                            <span class="text-neutral-400">·</span>
                            <span>{{ $post->author->name }}</span>
                        @endif
                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                            class="ml-auto inline-flex items-center gap-1 text-[13px] font-bold text-indigo-700 underline decoration-2 underline-offset-4 group-hover:text-indigo-900">{{ __('Oku') }} →</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border-2 border-dashed border-neutral-200 bg-neutral-50 py-16 text-center text-[15px] font-semibold text-neutral-600">
                    {{ __('Henüz yayınlanmış yazı yok.') }}</div>
            @endforelse
        </div>

        <div class="mt-10 flex justify-center">{{ $posts->links() }}</div>
    </div>
@endsection
