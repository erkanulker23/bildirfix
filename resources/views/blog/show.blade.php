@extends('layouts.app')

@section('title', $post->title.' • '.__('Blog').' • '.config('app.name'))

@section('content')
    @php
        $heroRaw = $post->hero_image_url;
        $heroPublic = filled($heroRaw)
            ? (\Illuminate\Support\Str::startsWith($heroRaw, ['http://', 'https://']) ? $heroRaw : url(ltrim($heroRaw, '/')))
            : null;
        $shareUrl = $shareUrl ?? route('blog.show', ['slug' => $post->slug], absolute: true);
        $shareTitle = $shareTitle ?? $post->title;
    @endphp

    <div class="blog-magazine mx-auto max-w-[1200px] px-4 pb-16 pt-2 sm:px-5">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-neutral-200 pb-4 sm:mb-8">
            <a href="{{ route('blog.index') }}"
                class="inline-flex items-center gap-1 text-sm font-bold text-neutral-600 transition hover:text-primary">
                ← {{ __('Blog') }}
            </a>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}"
                    class="rounded-full border border-neutral-200 bg-white px-4 py-2 text-xs font-bold text-neutral-800 transition hover:border-primary/40 hover:text-primary">
                    {{ __('Ana sayfa') }}
                </a>
                <a href="{{ route('feed.index') }}"
                    class="rounded-full bg-neutral-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-neutral-800">
                    {{ __('Akış') }}
                </a>
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[1fr_280px] lg:gap-10 xl:grid-cols-[1fr_300px]">
            <article class="min-w-0" itemscope itemtype="https://schema.org/Article">
                <meta itemprop="headline" content="{{ $post->title }}">

                @if ($heroPublic)
                    <div class="overflow-hidden rounded-3xl border border-neutral-200/80 shadow-[0_20px_50px_-20px_rgba(0,0,0,0.25)] ring-1 ring-black/[0.04]">
                        <img src="{{ $heroPublic }}" alt="" class="aspect-[21/9] w-full object-cover" itemprop="image"
                            loading="eager" decoding="async" fetchpriority="high">
                    </div>
                @endif

                <header class="{{ $heroPublic ? 'mt-8' : '' }} border-b border-neutral-100 pb-6 sm:pb-8">
                    @if ($post->category)
                        <span
                            class="inline-block rounded-md bg-orange-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-primary">{{ $post->category->name }}</span>
                    @endif
                    <h1 class="mt-3 font-heading text-[clamp(1.65rem,4vw,2.5rem)] font-black leading-[1.12] tracking-tight text-neutral-950"
                        itemprop="name">
                        {{ $post->title }}
                    </h1>
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-[13px] font-semibold text-neutral-500">
                        @if ($post->published_at)
                            <time itemprop="datePublished" datetime="{{ $post->published_at->toIso8601String() }}">
                                {{ $post->published_at->translatedFormat('d F Y') }}
                            </time>
                        @endif
                        @if ($post->author)
                            <span class="text-neutral-300" aria-hidden="true">·</span>
                            <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                                <span itemprop="name">{{ $post->author->name }}</span>
                            </span>
                        @endif
                    </div>
                    @if ($post->excerpt)
                        <p class="mt-5 text-lg font-medium leading-relaxed text-neutral-600" itemprop="description">
                            {{ $post->excerpt }}
                        </p>
                    @endif
                </header>

                <div class="blog-prose pt-8 text-[17px] leading-[1.75] text-neutral-800" itemprop="articleBody">
                    {!! $post->renderedBody() !!}
                </div>

                {{-- Mobil paylaşım --}}
                <div class="mt-10 lg:hidden">
                    <x-post-share-sidebar :url="$shareUrl" :title="$shareTitle" :heading="__('Sosyal medyada paylaş')" />
                </div>
            </article>

            <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                <x-post-share-sidebar :url="$shareUrl" :title="$shareTitle" :heading="__('Sosyal medyada paylaş')"
                    class="hidden lg:block" />

                <div class="rounded-2xl border border-neutral-200 bg-gradient-to-b from-neutral-50 to-white p-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">{{ __('Devam et') }}</p>
                    <p class="mt-2 font-heading text-lg font-black text-neutral-950">{{ __('Diğer yazılar') }}</p>
                    <a href="{{ route('blog.index') }}"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-bold text-neutral-900 transition hover:border-primary/40 hover:text-primary">
                        {{ __('Blog’a dön') }}
                    </a>
                </div>

                <div class="rounded-2xl border-l-4 border-primary bg-neutral-900 p-6 text-white shadow-lg">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-primary">{{ __('Platform') }}</p>
                    <p class="mt-2 font-heading text-lg font-black leading-snug">{{ __('Kent sorununu bildir, kampanyaya katıl.') }}</p>
                    <a href="{{ route('posts.create') }}"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-center text-sm font-black text-white transition hover:bg-primary-hover">
                        {{ __('Hemen bildir') }}
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection
