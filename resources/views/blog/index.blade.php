@extends('layouts.app')

@php
    $pageTitle = __('Blog').' • '.config('app.name');
    $blogHeroUrl = static function ($post): ?string {
        $u = $post->hero_image_url;
        if ($u === null || trim((string) $u) === '') {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($u, ['http://', 'https://']) ? $u : url(ltrim($u, '/'));
    };
@endphp
@section('title', $pageTitle)

@section('content')
    <div class="blog-magazine mx-auto max-w-[1200px] px-4 pb-16 pt-2 sm:px-5">
        {{-- Masthead --}}
        <header class="relative mb-8 border-b-2 border-neutral-900 pb-6 pt-2 sm:mb-10 sm:pb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="font-heading text-[clamp(2.5rem,6vw,4.5rem)] font-black leading-[0.95] tracking-tight text-neutral-950">
                        {{ __('Blog') }}
                    </p>
                    <p class="mt-2 max-w-lg font-medium text-[15px] leading-relaxed text-neutral-600">
                        {{ __('Kent, kampanya ve platform — magazin formatında haberler, rehberler ve derinlemesine yazılar.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <span class="hidden text-[11px] font-bold uppercase tracking-widest text-neutral-400 sm:inline">{{ now()->translatedFormat('d F Y') }}</span>
                    <a href="{{ route('home') }}"
                        class="rounded-full border border-neutral-200 bg-white px-4 py-2 text-xs font-bold text-neutral-800 transition hover:border-primary/40 hover:text-primary">
                        {{ __('Ana sayfa') }}
                    </a>
                    <a href="{{ route('feed.index') }}"
                        class="rounded-full bg-neutral-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-neutral-800">
                        {{ __('Akış') }}
                    </a>
                </div>
            </div>
            @if ($categories->isNotEmpty())
                <nav class="blog-mag-cats mt-6 flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    aria-label="{{ __('Kategoriler') }}">
                    <a href="{{ route('blog.index') }}"
                        class="shrink-0 rounded-full px-4 py-2 text-xs font-bold transition {{ $activeCategory === null ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200' }}">
                        {{ __('Tümü') }}
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('blog.index', ['kategori' => $cat->slug]) }}"
                            class="shrink-0 rounded-full px-4 py-2 text-xs font-bold transition {{ $activeCategory?->id === $cat->id ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-neutral-100 text-neutral-800 hover:bg-neutral-200' }}">
                            {{ $cat->name }}
                            <span class="ml-1 tabular-nums opacity-80">({{ $cat->posts_count }})</span>
                        </a>
                    @endforeach
                </nav>
            @endif
        </header>

        @if ($activeCategory !== null)
            <p class="mb-6 text-sm font-semibold text-neutral-600">
                <span class="text-neutral-400">{{ __('Kategori:') }}</span>
                <span class="text-neutral-900">{{ $activeCategory->name }}</span>
                · <a href="{{ route('blog.index') }}" class="font-bold text-primary underline-offset-2 hover:underline">{{ __('Tüm yazılar') }}</a>
            </p>
        @endif

        @if ($posts->isEmpty())
            <div class="rounded-3xl border-2 border-dashed border-neutral-200 bg-neutral-50 py-24 text-center">
                <p class="text-lg font-bold text-neutral-700">{{ __('Bu kategoride henüz yazı yok.') }}</p>
                <a href="{{ route('blog.index') }}"
                    class="btn-primary mt-6 inline-flex rounded-full px-6 py-3 text-sm font-bold">{{ __('Tüm yazılara dön') }}</a>
            </div>
        @else
            @php
                $showHero = $posts->onFirstPage() && $posts->count() > 0;
                $heroPost = $showHero ? $posts->first() : null;
            @endphp

            <div class="grid gap-8 lg:grid-cols-[1fr_300px] lg:gap-10 xl:grid-cols-[1fr_320px]">
                <div class="min-w-0 space-y-8">
                    @if ($heroPost !== null)
                        @php $heroUrl = $blogHeroUrl($heroPost); @endphp
                        <article
                            class="blog-mag-hero group relative overflow-hidden rounded-3xl bg-neutral-950 shadow-[0_24px_60px_-12px_rgba(0,0,0,0.35)] ring-1 ring-white/10">
                            <a href="{{ route('blog.show', ['slug' => $heroPost->slug]) }}" class="relative block">
                                @if ($heroUrl)
                                    <div class="aspect-[16/10] w-full sm:aspect-[21/9]">
                                        <img src="{{ $heroUrl }}" alt="" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
                                            loading="eager" decoding="async" fetchpriority="high">
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent sm:via-black/30"></div>
                                @else
                                    <div
                                        class="flex aspect-[16/10] min-h-[14rem] items-end bg-gradient-to-br from-primary via-orange-600 to-neutral-900 p-8 sm:aspect-[21/9] sm:min-h-[16rem]">
                                    </div>
                                @endif
                                <div class="{{ $heroUrl ? 'absolute inset-x-0 bottom-0 p-6 sm:p-10' : 'absolute inset-x-0 bottom-0 p-6 sm:p-10' }}">
                                    @if ($heroPost->category)
                                        <span
                                            class="inline-block rounded-full bg-primary px-3 py-1 text-[10px] font-black uppercase tracking-wider text-white">{{ $heroPost->category->name }}</span>
                                    @endif
                                    <h2
                                        class="blog-mag-hero__title mt-3 font-heading text-[clamp(1.35rem,3.5vw,2.35rem)] font-black leading-[1.1] tracking-tight text-white drop-shadow-sm">
                                        {{ $heroPost->title }}
                                    </h2>
                                    @if ($heroPost->excerpt)
                                        <p class="mt-3 line-clamp-2 max-w-2xl text-[15px] font-medium leading-relaxed text-white/90 sm:line-clamp-3">
                                            {{ \Illuminate\Support\Str::limit(strip_tags((string) $heroPost->excerpt), 220) }}</p>
                                    @endif
                                    <div class="mt-4 flex flex-wrap items-center gap-3 text-[13px] font-semibold text-white/85">
                                        @if ($heroPost->published_at)
                                            <time
                                                datetime="{{ $heroPost->published_at->toIso8601String() }}">{{ $heroPost->published_at->translatedFormat('d M Y') }}</time>
                                        @endif
                                        @if ($heroPost->author)
                                            <span class="text-white/50" aria-hidden="true">·</span>
                                            <span>{{ $heroPost->author->name }}</span>
                                        @endif
                                        <span class="ml-auto inline-flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 text-white backdrop-blur-sm transition group-hover:bg-white/25">
                                            {{ __('Kapak haberi') }} →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2 sm:gap-6">
                        @foreach ($posts as $post)
                            @if ($heroPost !== null && $loop->first)
                                @continue
                            @endif
                            @php $cardUrl = $blogHeroUrl($post); @endphp
                            <article
                                class="blog-mag-card group flex flex-col overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-sm ring-1 ring-black/[0.03] transition hover:-translate-y-0.5 hover:border-primary/25 hover:shadow-lg hover:shadow-primary/5">
                                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="relative block shrink-0 overflow-hidden">
                                    @if ($cardUrl)
                                        <div class="aspect-[16/10] w-full">
                                            <img src="{{ $cardUrl }}" alt=""
                                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy" decoding="async">
                                        </div>
                                    @else
                                        <div
                                            class="blog-mag-card__placeholder flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-neutral-100 via-orange-50/80 to-neutral-100">
                                            <span class="font-heading text-4xl font-black text-primary/30" aria-hidden="true">
                                                {{ mb_substr(strip_tags($post->title), 0, 1) }}</span>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    @if ($post->category)
                                        <span
                                            class="w-fit rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wide text-primary">{{ $post->category->name }}</span>
                                    @endif
                                    <h3 class="mt-2 font-heading text-lg font-black leading-snug tracking-tight text-neutral-950 sm:text-xl">
                                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                                            class="hover:text-primary">{{ $post->title }}</a>
                                    </h3>
                                    @if ($post->excerpt)
                                        <p class="mt-2 line-clamp-3 flex-1 text-[14px] leading-relaxed text-neutral-600">
                                            {{ \Illuminate\Support\Str::limit(strip_tags((string) $post->excerpt), 140) }}</p>
                                    @endif
                                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-neutral-100 pt-3 text-[12px] font-semibold text-neutral-500">
                                        @if ($post->published_at)
                                            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('d M Y') }}</time>
                                        @endif
                                        @if ($post->author)
                                            <span class="text-neutral-300">·</span>
                                            <span class="truncate">{{ $post->author->name }}</span>
                                        @endif
                                        <a href="{{ route('blog.show', ['slug' => $post->slug]) }}"
                                            class="ml-auto text-xs font-black uppercase tracking-wide text-primary hover:underline">{{ __('Oku') }}</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="flex justify-center pt-4">{{ $posts->links() }}</div>
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
                    <div class="rounded-2xl border border-neutral-200 bg-gradient-to-b from-neutral-50 to-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">{{ __('Bu sayıda') }}</p>
                        <p class="mt-2 font-heading text-xl font-black text-neutral-950">{{ __('Öne çıkanlar') }}</p>
                        <ul class="mt-5 space-y-4">
                            @foreach ($posts->take(5) as $i => $sidePost)
                                <li class="flex gap-3 border-b border-neutral-100 pb-4 last:border-0 last:pb-0">
                                    <span
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-neutral-900 font-heading text-sm font-black text-white">{{ $i + 1 }}</span>
                                    <div class="min-w-0">
                                        @if ($sidePost->category)
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-neutral-400">{{ $sidePost->category->name }}</p>
                                        @endif
                                        <a href="{{ route('blog.show', ['slug' => $sidePost->slug]) }}"
                                            class="mt-0.5 line-clamp-2 text-sm font-bold leading-snug text-neutral-900 hover:text-primary">{{ $sidePost->title }}</a>
                                        @if ($sidePost->published_at)
                                            <p class="mt-1 text-[11px] font-semibold text-neutral-400">{{ $sidePost->published_at->translatedFormat('d M Y') }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
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
        @endif
    </div>
@endsection
