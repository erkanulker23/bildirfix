{{-- Kent odaklı hero — tam görünüm genişliği (akış sayfası için partial) --}}
<section
    class="relative left-1/2 z-0 mb-10 w-screen max-w-[100vw] -translate-x-1/2 rounded-none border-y border-neutral-200/85 bg-[#e8ecf2] shadow-[inset_0_1px_0_rgba(255,255,255,0.72)] outline outline-1 outline-black/[0.03]"
    aria-labelledby="hero-baslik">
    <div class="pointer-events-none absolute inset-x-8 top-[15%] h-52 rounded-full bg-sky-200/35 blur-[48px]"
        aria-hidden="true"></div>
    <div
        class="relative z-[1] mx-auto grid max-w-[1250px] gap-10 px-5 py-10 sm:gap-12 sm:px-8 sm:py-14 lg:grid-cols-[minmax(0,1.06fr)_minmax(260px,.94fr)] lg:items-center lg:gap-14">
        <div>
            <h1 id="hero-baslik" class="text-[clamp(1.75rem,3.8vw,2.85rem)] font-black leading-[1.07] tracking-tight text-gray-900">
                {{ __('Kent yaşamına çözüm için') }}
                {{ config('app.name') }}</h1>
            <p class="mt-4 max-w-lg text-[15px] font-medium leading-relaxed text-gray-600">
                {{ __('Kaldırım, çevre, ulaşım ve benzeri kent yaşamı bildirimi; fotoğraf ve konum ile kuruma görünür kılın.') }}</p>
            @if (! empty($geoActive ?? false))
                <p class="mt-3 max-w-lg rounded-xl border border-gray-300/70 bg-black/[0.04] px-3 py-2 text-[13px] font-semibold text-gray-900">
                    {{ __('Konum sıralaması açık.') }}</p>
            @endif

            <form method="get" action="{{ route('home') }}" role="search"
                class="mt-9 flex flex-col gap-2 rounded-full border border-neutral-200/70 bg-white p-2 shadow-[0_12px_40px_-22px_rgba(15,23,42,0.35)] sm:flex-row sm:items-center sm:rounded-full">
                @foreach (request()->only(['city_id', 'relax_city', 'lat', 'lng', 'feed']) as $k => $v)
                    @if (! (is_string($v) && trim($v) === '') && $v !== null)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <label class="sr-only" for="hero-arama">{{ __('Kent sorununda ara…') }}</label>
                <div class="flex min-h-[3.125rem] min-w-0 flex-1 items-center gap-2 pl-5 text-gray-500">
                    <svg class="h-6 w-6 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                    </svg>
                    <input id="hero-arama" type="search" name="q" value="{{ old('q', $searchQuery ?? '') }}"
                        autocomplete="off" placeholder="{{ __('Kent sorununun özünü ara…') }}"
                        class="w-full flex-1 border-0 bg-transparent py-3 text-[16px] font-medium text-gray-900 outline-none placeholder:text-gray-500 sm:text-[15px]">
                </div>
                <button type="submit"
                    class="shrink-0 rounded-full bg-[#34d399] px-7 py-3.5 text-[14px] font-black text-neutral-950 shadow-md shadow-teal-500/35 transition hover:bg-emerald-300 sm:self-stretch md:rounded-full md:py-3">
                    {{ __('Ara') }}
                </button>
            </form>

            <div class="mt-7 flex flex-wrap items-center gap-2">
                @auth
                    <a href="{{ route('posts.create') }}"
                        class="inline-flex items-center justify-center rounded-full bg-primary px-5 py-2.5 text-[13px] font-bold text-white shadow-cta transition hover:bg-primary-hover">{{ __('Kent sorunu bildir') }}</a>
                    <a href="{{ route('feed.index') }}"
                        class="inline-flex items-center justify-center rounded-full border border-gray-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-gray-900 hover:bg-white/80">{{ __('Canlı akış') }}</a>
                @else
                    <a href="{{ route('posts.create') }}"
                        class="inline-flex items-center justify-center rounded-full bg-primary px-5 py-2.5 text-[13px] font-bold text-white shadow-cta transition hover:bg-primary-hover">{{ __('Kent sorunu bildir') }}</a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center rounded-full border border-gray-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-gray-900 hover:bg-white/80">{{ __('Üye ol') }}</a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-full border border-gray-300/90 bg-transparent px-4 py-2.5 text-[13px] font-bold text-gray-900 hover:bg-white/80">{{ __('Giriş') }}</a>
                @endauth
                <a href="{{ route('contact') }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-[13px] font-semibold text-gray-600 underline underline-offset-4 hover:text-primary">{{ __('İletişim') }}</a>
            </div>
        </div>

        <div class="relative mx-auto isolate aspect-square w-full max-w-[min(100%,460px)] sm:max-w-md lg:max-w-none"
            aria-hidden="true">
            <div class="absolute -right-[6%] -top-[4%] h-[62%] w-[58%] overflow-hidden rounded-3xl bg-primary shadow-xl ring-[5px] ring-white">
                <img src="{{ asset('images/hero/collage-woman-purple.jpg') }}" alt=""
                    class="h-full w-full object-cover object-[center_top] mix-blend-luminosity" loading="lazy" decoding="async">
                <span class="absolute inset-0 bg-gradient-to-br from-[#8b7cff]/50 to-[#422dc7]/65 mix-blend-color"></span>
            </div>
            <div
                class="absolute bottom-[26%] left-0 h-[43%] w-[43%] overflow-hidden rounded-full border-[6px] border-white bg-neutral-900 shadow-xl ring-[3px] ring-[#34d399]/85">
                <img src="{{ asset('images/hero/collage-man-circle.jpg') }}" alt=""
                    class="h-full w-full object-cover object-top" loading="lazy" decoding="async">
            </div>
            <div
                class="absolute bottom-[-2%] right-[6%] h-[54%] w-[52%] overflow-hidden rounded-[2rem] bg-gradient-to-br from-amber-300 to-orange-400 p-[6px] shadow-xl ring-[5px] ring-white">
                <div class="h-full w-full overflow-hidden rounded-[1.55rem]">
                    <img src="{{ asset('images/hero/collage-woman-yellow.jpg') }}" alt=""
                        class="h-full w-full object-cover object-center" loading="lazy" decoding="async">
                </div>
            </div>
            <div
                class="absolute left-[4%] top-[8%] flex h-[28%] w-[36%] items-center justify-center gap-1.5 rounded-3xl bg-primary shadow-lg ring-2 ring-white/40">
                <span class="h-2.5 w-2.5 rounded-full bg-white/95"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-white/65"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-white/40"></span>
            </div>
            <div
                class="absolute left-[52%] top-[14%] flex h-[15%] w-[15%] min-h-[52px] min-w-[52px] items-center justify-center rounded-full bg-amber-300 shadow-md ring-[3px] ring-white">
                <svg class="h-[55%] w-[55%] text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path
                        d="M11.049 3.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.887a1 1 0 00-1.176 0l-3.976 2.887c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div class="pointer-events-none absolute left-[-14%] top-[52%] h-[42%] w-[36%] rounded-full bg-teal-200/45 blur-2xl">
            </div>
            <div class="pointer-events-none absolute bottom-[12%] left-[38%] h-[26%] w-[22%] rounded-full bg-neutral-900/85"></div>
            <div
                class="pointer-events-none absolute -bottom-[8%] -left-[8%] h-[42%] w-[48%] rounded-full border-[14px] border-teal-200/80 border-l-transparent border-t-transparent">
            </div>
        </div>
    </div>
</section>
