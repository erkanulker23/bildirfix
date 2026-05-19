@extends('layouts.app')

@section('title', $post->title.' • '.__('Blog').' • '.config('app.name'))

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-end gap-3 sm:justify-end">
        <a href="{{ route('blog.index') }}"
            class="rounded-2xl border border-indigo-100 bg-white px-4 py-2 text-sm font-bold text-indigo-900 shadow-sm hover:bg-indigo-50">
            ← {{ __('Blog') }}</a>
        <a href="{{ route('home') }}"
            class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-indigo-700">{{ __('Akış') }}</a>
    </div>
    <article class="mx-auto max-w-3xl" itemscope itemtype="https://schema.org/Article">
        <meta itemprop="headline" content="{{ $post->title }}">
        @if ($post->hero_image_url)
            @php
                $heroRaw = $post->hero_image_url;
                $heroPublic = \Illuminate\Support\Str::startsWith($heroRaw, ['http://', 'https://']) ? $heroRaw : url(ltrim($heroRaw, '/'));
            @endphp
            <div class="overflow-hidden rounded-3xl border border-neutral-100 shadow-xl shadow-neutral-900/10">
                <img src="{{ $heroPublic }}"
                    alt="" class="aspect-[21/9] w-full object-cover" itemprop="image" loading="lazy" decoding="async">
            </div>
        @endif

        <header class="mt-8 border-b border-neutral-100 pb-8">
            <h1 class="text-[clamp(1.65rem,4vw,2.35rem)] font-black leading-tight tracking-tight text-neutral-900" itemprop="name">
                {{ $post->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-2 text-[13px] font-semibold text-neutral-500">
                @if ($post->published_at)
                    <time itemprop="datePublished" datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('d F Y, H:i') }}</time>
                @endif
                @if ($post->author)
                    <span class="text-neutral-300" aria-hidden="true">|</span>
                    <span itemprop="author" itemscope itemtype="https://schema.org/Person"><span itemprop="name">{{ $post->author->name }}</span></span>
                @endif
            </div>
            @if ($post->excerpt)
                <p class="mt-5 text-lg font-medium leading-relaxed text-neutral-600" itemprop="description">{{ $post->excerpt }}</p>
            @endif
        </header>

        <div class="blog-prose pt-8 text-[17px] leading-[1.75] text-neutral-800" itemprop="articleBody">
            {!! $post->renderedBody() !!}
        </div>
    </article>
@endsection
