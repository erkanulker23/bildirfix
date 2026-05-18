{{-- Ana sayfa: kısa «Nasıl çalışır» özeti — detay: route('how-it-works') --}}
<section class="mb-10 scroll-mt-8 sm:mb-12" aria-labelledby="nasil-calisir-ozet">
    <div class="home-container">
        <div
            class="rounded-3xl border border-neutral-200/80 bg-gradient-to-br from-white via-violet-50/30 to-emerald-50/25 px-5 py-6 shadow-[0_16px_48px_-28px_rgba(91,33,182,0.2)] ring-1 ring-black/[0.03] sm:px-8 sm:py-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-black uppercase tracking-widest text-violet-800">{{ __('Nasıl çalışır?') }}</p>
                    <h2 id="nasil-calisir-ozet" class="mt-2 font-heading text-[clamp(1.1rem,2.2vw,1.45rem)] font-black leading-tight text-neutral-950">
                        {{ __('Üç adımda bildir, toplulukla görünür kıl') }}
                    </h2>
                    <p class="mt-2 max-w-xl text-[13px] leading-relaxed text-neutral-600">
                        {{ __('Fotoğraf ve konumla kayıt; moderasyon sonrası yayın. Resmî başvuru yerine geçmez.') }}
                    </p>
                </div>
                <a href="{{ route('how-it-works') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-full border border-violet-200 bg-white px-4 py-2 text-[12px] font-bold text-violet-800 shadow-sm hover:bg-violet-50">
                    {{ __('Tüm rehber →') }}
                </a>
            </div>

            <ol class="mt-6 grid gap-3 sm:grid-cols-3 sm:gap-4">
                @foreach ([
                    ['n' => 1, 't' => __('Konu ve medya'), 'd' => __('Ne oldu, kategori; isteğe bağlı fotoğraf veya video.')],
                    ['n' => 2, 't' => __('Konum'), 'd' => __('İl, ilçe, mahalle; haritadan tam nokta.')],
                    ['n' => 3, 't' => __('Yayın ve takip'), 'd' => __('Onay sonrası akışta; destek ver, süreci izle.')],
                ] as $step)
                    <li
                        class="flex gap-3 rounded-2xl border border-white/80 bg-white/90 p-4 shadow-sm ring-1 ring-neutral-200/60">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-black text-primary ring-1 ring-primary/20">{{ $step['n'] }}</span>
                        <div class="min-w-0">
                            <h3 class="text-sm font-black text-neutral-900">{{ $step['t'] }}</h3>
                            <p class="mt-1 text-[12px] leading-snug text-neutral-600">{{ $step['d'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ route('posts.create') }}"
                    class="inline-flex items-center gap-1 rounded-full bg-violet-600 px-4 py-2 text-[12px] font-bold text-white shadow-md hover:bg-violet-700">
                    <span aria-hidden="true">+</span> {{ __('Kent sorunu bildir') }}
                </a>
                <a href="{{ route('feed.index') }}"
                    class="text-[12px] font-semibold text-neutral-600 underline-offset-2 hover:text-violet-700">{{ __('Canlı akış') }}</a>
            </div>
        </div>
    </div>
</section>
