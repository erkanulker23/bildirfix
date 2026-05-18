@extends('layouts.app')

@section('title', __('Sayfa bulunamadı').' • '.config('app.name'))

@section('content')
    <section class="mx-auto flex min-h-[min(70vh,640px)] max-w-2xl flex-col items-center justify-center px-4 py-16 text-center">
        <div class="relative mb-8" aria-hidden="true">
            <div class="flex h-28 w-28 items-center justify-center rounded-[2rem] bg-primary-light ring-1 ring-primary/20">
                <svg class="h-14 w-14 text-primary" fill="none" viewBox="0 0 64 64" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20 44V28a12 12 0 0124 0v16M16 44h32M28 44v6a4 4 0 008 0v-6" />
                    <circle cx="32" cy="20" r="4" fill="currentColor" stroke="none" opacity="0.35" />
                </svg>
            </div>
            <span class="absolute -right-2 -top-2 rounded-full bg-neutral-900 px-3 py-1 text-xs font-black text-white shadow-lg">404</span>
        </div>

        <p class="text-[11px] font-black uppercase tracking-[0.22em] text-primary">{{ __('Sayfa bulunamadı') }}</p>
        <h1 class="mt-3 text-[clamp(1.75rem,4vw,2.5rem)] font-black tracking-tight text-neutral-950">
            {{ __('Aradığınız sayfa burada değil') }}
        </h1>
        <p class="mt-4 max-w-md text-[15px] font-medium leading-relaxed text-neutral-600">
            {{ __('Bağlantı kırılmış veya sayfa taşınmış olabilir. Ana sayfadan devam edebilir, şehrinizi seçebilir veya kampanyalara göz atabilirsiniz.') }}
        </p>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}"
                class="btn-primary inline-flex rounded-full px-7 py-3.5 text-[13px] font-black uppercase tracking-wider">
                {{ __('Ana sayfa') }}
            </a>
            <a href="{{ route('cities.explore') }}"
                class="inline-flex rounded-full bg-neutral-100 px-6 py-3.5 text-[13px] font-bold text-neutral-900 ring-1 ring-neutral-200 hover:bg-neutral-200">
                {{ __('Şehrini keşfet') }}
            </a>
            <a href="{{ route('campaigns.index') }}"
                class="inline-flex rounded-full bg-neutral-100 px-6 py-3.5 text-[13px] font-bold text-neutral-900 ring-1 ring-neutral-200 hover:bg-neutral-200">
                {{ __('Kampanyalar') }}
            </a>
        </div>

        <form action="{{ route('feed.index') }}" method="get" class="mt-10 w-full max-w-md">
            <label class="sr-only" for="404-search">{{ __('Ara') }}</label>
            <div class="flex gap-2 rounded-2xl border border-neutral-200 bg-white p-2 shadow-sm ring-1 ring-black/[0.03]">
                <input id="404-search" name="q" type="search" placeholder="{{ __('Şikâyet veya konu ara…') }}"
                    class="min-w-0 flex-1 rounded-xl border-0 bg-transparent px-3 py-2.5 text-sm font-semibold text-neutral-900 outline-none focus:ring-0">
                <button type="submit"
                    class="shrink-0 rounded-xl bg-primary px-5 py-2.5 text-xs font-black uppercase tracking-wide text-white hover:bg-primary-hover">
                    {{ __('Ara') }}
                </button>
            </div>
        </form>
    </section>
@endsection
