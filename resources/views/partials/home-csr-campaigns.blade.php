{{-- Kampanya / CSR şeridi --}}
<section
    class="home-fluid relative z-0 mb-10 overflow-hidden rounded-3xl bg-gradient-to-br from-white via-indigo-50/90 to-violet-100/80 px-5 py-8 shadow-[0_24px_60px_-40px_rgba(67,56,202,0.45)] sm:px-8 sm:py-10"
    aria-labelledby="kampanya-csr-baslik">
    <div class="pointer-events-none absolute -right-24 -top-16 h-56 w-56 rounded-full bg-violet-400/25 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-16 h-48 w-48 rounded-full bg-emerald-300/30 blur-3xl" aria-hidden="true"></div>
    <div class="relative z-10 mx-auto grid max-w-[1250px] gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(260px,.95fr)] lg:items-center">
        <div>
            <p
                class="inline-flex rounded-full border border-indigo-200/90 bg-white/80 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.18em] text-indigo-900 shadow-sm backdrop-blur-sm">
                {{ __('Sosyal sorumluluk') }}</p>
            <h2 id="kampanya-csr-baslik"
                class="mt-4 text-[clamp(1.35rem,2.8vw,1.95rem)] font-black leading-tight tracking-tight text-neutral-950">
                {{ __('Toplumu güçlendiren kampanyalar — süper yönetici onayı ile yayında.') }}</h2>
            <p class="mt-4 max-w-2xl text-[15px] font-medium leading-relaxed text-neutral-700">
                {{ __('Kent sorununun yanında, dayanışma ve kolektif aksiyon içeren kampanyaları burada listeleyebilir, destekçi kitle oluşturabilirsiniz. Her kampanya süper yönetici incelemesinden sonra herkese açılır.') }}</p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('campaigns.index') }}"
                    class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-6 py-3 text-[13px] font-black text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700">
                    {{ __('Kampanyaları keşfet') }}</a>
                @auth
                    <a href="{{ route('campaigns.create') }}"
                        class="inline-flex items-center justify-center rounded-full border border-neutral-900/15 bg-white px-5 py-3 text-[13px] font-bold text-neutral-900 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50/60">{{ __('Yeni kampanya başlat') }}</a>
                @else
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center rounded-full border border-neutral-900/15 bg-white px-5 py-3 text-[13px] font-bold text-neutral-900 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50/60">{{ __('Katıl ve kampanya aç') }}</a>
                @endauth
            </div>
        </div>
        <div class="rounded-3xl border border-indigo-200/70 bg-white/90 p-5 shadow-xl shadow-indigo-900/10 backdrop-blur-md sm:p-6">
            <p class="text-[11px] font-black uppercase tracking-wider text-emerald-700">{{ __('Canlı kampanyalar') }}</p>
            @if (($featuredCampaigns ?? collect())->isEmpty())
                <p class="mt-4 text-[15px] font-semibold leading-snug text-neutral-700">{{ __('Onaylı kampanya oluştukça burada listelenecek. İlk destekçilerden olun!') }}</p>
                <a href="{{ route('campaigns.index') }}"
                    class="mt-4 inline-flex text-[13px] font-black text-indigo-700 underline decoration-4 underline-offset-4 hover:text-indigo-900">{{ __('Tüm kampanyalar') }}</a>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($featuredCampaigns->take(4) as $c)
                        <li class="rounded-2xl border border-neutral-200/80 bg-neutral-50/60 px-4 py-3">
                            <a href="{{ route('campaigns.show', $c) }}"
                                class="block text-[15px] font-black tracking-tight text-neutral-950 hover:text-indigo-800">{{ $c->title }}</a>
                            <p class="mt-1 line-clamp-2 text-[13px] font-medium text-neutral-600">
                                {{ trim((string) ($c->excerpt ?? '')) !== '' ? \Illuminate\Support\Str::limit(trim((string) $c->excerpt), 120) : \Illuminate\Support\Str::limit(strip_tags((string) $c->description), 120) }}</p>
                            <p class="mt-2 text-[11px] font-bold uppercase tracking-wide text-emerald-800">
                                {{ number_format(max(0, (int) $c->supporter_count)) }}
                                {{ __('destekçi') }}
                                @if ($c->goal_supporters)
                                    · {{ __('hedef :n', ['n' => number_format((int) $c->goal_supporters)]) }}
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('campaigns.index') }}"
                    class="mt-5 inline-flex w-full justify-center rounded-2xl bg-emerald-500 px-4 py-3 text-[12px] font-black uppercase tracking-wider text-neutral-950 shadow-md ring-1 ring-emerald-700/20 hover:bg-emerald-400">{{ __('Liste ve filtre →') }}</a>
            @endif
        </div>
    </div>
</section>
