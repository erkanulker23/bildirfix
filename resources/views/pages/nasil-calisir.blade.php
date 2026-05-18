@extends('layouts.app')

@section('title', __('Nasıl çalışır?').' • '.config('app.name'))

@php
    $seo = [
        'description' => __('Kent sorunlarını fotoğraf ve konumla nasıl bildireceğinizi, moderasyon sürecini, destek ve takip özelliklerini adım adım öğrenin.'),
        'canonical' => route('how-it-works'),
        'og_title' => __('Nasıl çalışır?').' • '.config('app.name'),
    ];
    $structuredData = [
        \App\Support\Seo::breadcrumbStructuredData([
            [__('Ana sayfa'), route('home')],
            [__('Nasıl çalışır?'), route('how-it-works')],
        ]),
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section
        class="relative left-1/2 z-0 mb-12 w-screen max-w-[100vw] -translate-x-1/2 border-b border-neutral-200/80 bg-gradient-to-b from-neutral-50 via-white to-[#eef1f8]">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-48 bg-gradient-to-b from-violet-100/40 to-transparent blur-2xl"
            aria-hidden="true"></div>
        <div class="relative mx-auto max-w-[900px] px-4 py-12 sm:px-5 sm:py-16 lg:py-20">
            <p
                class="inline-flex rounded-full bg-violet-50 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-violet-800 ring-1 ring-violet-200/80">
                {{ __('Rehber') }}
            </p>
            <h1 class="mt-4 text-balance font-heading text-[clamp(1.85rem,4.5vw,2.75rem)] font-black leading-[1.08] tracking-tight text-neutral-900">
                {{ config('app.name') }} {{ __('nasıl çalışır?') }}
            </h1>
            <p class="mt-4 max-w-2xl text-[15px] leading-relaxed text-neutral-700">
                {{ __('Kaldırım, çevre, ulaşım ve benzeri kent yaşamı sorunlarını fotoğraf, video ve konumla paylaşın; topluluk desteğiyle görünür kılın. Platform resmi başvuru kanallarının yerini almaz — süreci şeffaflaştırmak için tasarlandı.') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('posts.create') }}"
                    class="inline-flex items-center gap-1.5 rounded-full bg-violet-600 px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_6px_20px_-4px_rgba(91,33,182,0.45)] transition hover:bg-violet-700">
                    <span class="text-base font-black leading-none" aria-hidden="true">+</span>
                    {{ __('Kent sorunu bildir') }}
                </a>
                <a href="{{ route('feed.index') }}"
                    class="inline-flex items-center justify-center rounded-full border border-neutral-300/90 bg-white px-4 py-2.5 text-[13px] font-bold text-neutral-800 transition hover:border-violet-300 hover:bg-violet-50/50">
                    {{ __('Canlı akışa göz at') }}
                </a>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-[900px] space-y-16 pb-20 sm:space-y-20">
        {{-- Platform özeti --}}
        <section aria-labelledby="platform-ozet">
            <h2 id="platform-ozet" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Platform ne işe yarar?') }}
            </h2>
            <p class="mt-3 text-[15px] leading-relaxed text-neutral-700">
                {{ __('Vatandaşlar mahalle ve şehir ölçeğinde yaşadıkları sorunları kayda geçirir; diğer kullanıcılar destek vererek ve yorum yaparak konuyu güçlendirir. İçerikler yayına alınmadan önce moderasyondan geçer. Belediye, dağıtım şirketi ve benzeri kurumların profilleri üzerinden ilgili bildirimler gruplanabilir.') }}
            </p>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['icon' => 'camera', 'title' => __('Görsel kanıt'), 'text' => __('Fotoğraf veya video ile sorunu net gösterin.')],
                    ['icon' => 'map', 'title' => __('Konum'), 'text' => __('İl, ilçe, mahalle ve harita koordinatı.')],
                    ['icon' => 'users', 'title' => __('Topluluk'), 'text' => __('Destek, takip ve yorumlarla süreç izlenir.')],
                ] as $card)
                    <div
                        class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-[0_4px_24px_-12px_rgba(15,23,42,0.12)] ring-1 ring-black/[0.04]">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-light text-primary ring-1 ring-primary/15">
                            @if ($card['icon'] === 'camera')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @elseif ($card['icon'] === 'map')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                        </div>
                        <h3 class="mt-4 font-heading text-sm font-black text-neutral-900">{{ $card['title'] }}</h3>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-neutral-600">{{ $card['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Bildirim oluşturma --}}
        <section aria-labelledby="bildirim-adimlari">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="bildirim-adimlari" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                        {{ __('Bildirim nasıl oluşturulur?') }}
                    </h2>
                    <p class="mt-2 text-[14px] text-neutral-600">
                        {{ __('Üç adımlı sihirbaz: konu ve muhataplar, konum, özet başlık.') }}
                    </p>
                </div>
                <a href="{{ route('posts.create') }}"
                    class="text-[13px] font-bold text-violet-700 underline-offset-4 hover:underline">{{ __('Hemen başla →') }}</a>
            </div>
            <ol class="mt-8 space-y-4">
                @foreach ([
                    [
                        'step' => 1,
                        'title' => __('Konuyu yazın'),
                        'desc' => __('Ne olduğunu anlatın, kategori seçin. Fotoğraf veya video ekleyebilir; ilgili kurumu (belediye, dağıtım şirketi vb.) arayıp muhatap olarak işaretleyebilirsiniz.'),
                    ],
                    [
                        'step' => 2,
                        'title' => __('Konumu seçin'),
                        'desc' => __('İl, ilçe ve mahalle bilgisini girin. İsterseniz haritadan tam koordinat işaretleyin; komşuların sorunu bulması kolaylaşır.'),
                    ],
                    [
                        'step' => 3,
                        'title' => __('Özet başlık ve gönder'),
                        'desc' => __('Akışta ve arama sonuçlarında görünecek kısa bir başlık yazın. Gönderimden sonra moderasyon süreci başlar.'),
                    ],
                ] as $step)
                    <li
                        class="flex gap-4 rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-sm ring-1 ring-black/[0.03] sm:gap-5 sm:p-6">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-light text-base font-black text-primary ring-1 ring-primary/20">{{ $step['step'] }}</span>
                        <div class="min-w-0">
                            <h3 class="font-heading text-base font-black text-neutral-900">{{ $step['title'] }}</h3>
                            <p class="mt-1.5 text-[13px] leading-relaxed text-neutral-600">{{ $step['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
            <div
                class="mt-5 rounded-xl border border-amber-200/90 bg-amber-50 px-4 py-3.5 text-[13px] font-medium leading-relaxed text-amber-950">
                <strong class="font-black">{{ __('Misafir kullanıcı:') }}</strong>
                {{ __('Üye olmadan formu doldurabilirsiniz; taslak kaydedilir. Kayıt ve telefon doğrulamasından sonra bildiriminiz otomatik tamamlanır.') }}
            </div>
        </section>

        {{-- Üyelik --}}
        <section aria-labelledby="uyelik-guven">
            <h2 id="uyelik-guven" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Üyelik ve güven') }}
            </h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 ring-1 ring-emerald-100/80">
                    <h3 class="text-sm font-black text-emerald-950">{{ __('Kayıt ve giriş') }}</h3>
                    <p class="mt-2 text-[13px] leading-relaxed text-emerald-900/90">
                        {{ __('E-posta ile üye olun veya Google hesabınızla giriş yapın. Kampanya başlatma, destek verme ve takip gibi işlemler için giriş gerekir.') }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('register') }}"
                            class="rounded-full bg-emerald-700 px-3.5 py-1.5 text-[12px] font-bold text-white hover:bg-emerald-800">{{ __('Üye ol') }}</a>
                        <a href="{{ route('login') }}"
                            class="rounded-full border border-emerald-300 bg-white px-3.5 py-1.5 text-[12px] font-bold text-emerald-900 hover:bg-emerald-50">{{ __('Giriş') }}</a>
                    </div>
                </div>
                <div class="rounded-2xl border border-neutral-200 bg-white p-5 ring-1 ring-black/[0.04]">
                    <h3 class="text-sm font-black text-neutral-900">{{ __('Telefon doğrulama') }}</h3>
                    <p class="mt-2 text-[13px] leading-relaxed text-neutral-600">
                        {{ __('İçerik oluşturmadan ve kampanya başlatmadan önce SMS ile telefon numaranızı doğrulamanız istenir. Bu, sahte hesapları azaltır ve topluluk güvenini artırır.') }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Moderasyon ve durumlar --}}
        <section aria-labelledby="moderasyon-durum">
            <h2 id="moderasyon-durum" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Moderasyon ve şikâyet durumları') }}
            </h2>
            <p class="mt-3 text-[15px] leading-relaxed text-neutral-700">
                {{ __('Her bildirim yayına alınmadan önce incelenir. Uygunsuz, yanıltıcı veya kişisel veri ihlali içeren içerikler reddedilebilir veya yayından kaldırılabilir.') }}
            </p>
            <div class="mt-6 flex flex-wrap gap-2">
                @foreach ([
                    __('Onay bekliyor'),
                    __('Yayında'),
                    __('Yayından kaldırıldı'),
                ] as $modLabel)
                    <span
                        class="inline-flex rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-[12px] font-bold text-neutral-700">{{ $modLabel }}</span>
                @endforeach
            </div>
            <h3 class="mt-8 text-sm font-black uppercase tracking-wide text-neutral-500">{{ __('Çözüm süreci etiketleri') }}
            </h3>
            <div class="mt-3 flex flex-wrap gap-2">
                <span class="badge badge-open text-xs">{{ __('Açık') }}</span>
                <span class="badge badge-progress text-xs">{{ __('İnceleniyor') }}</span>
                <span class="badge badge-resolved text-xs">{{ __('Çözüldü') }}</span>
                <span class="badge badge-rejected text-xs">{{ __('Reddedildi') }}</span>
            </div>
            <p class="mt-4 text-[13px] leading-relaxed text-neutral-600">
                {{ __('Durum güncellemeleri şikâyet sayfasında görünür. «Takip et» derseniz süreçteki değişiklikler hakkında bildirim alabilirsiniz.') }}
            </p>
        </section>

        {{-- Topluluk etkileşimi --}}
        <section aria-labelledby="topluluk">
            <h2 id="topluluk" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Topluluk nasıl katkı sağlar?') }}
            </h2>
            <ul class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['title' => __('Destek ver'), 'text' => __('Aynı sorunu yaşayanlar tek tıkla destekler; sayı görünürlüğü artar.')],
                    ['title' => __('Takip et'), 'text' => __('Çözüm sürecindeki güncellemeler için bildirim alırsınız.')],
                    ['title' => __('Yorum yap'), 'text' => __('Ek bilgi veya deneyim paylaşın; moderasyon uygulanır.')],
                ] as $item)
                    <li class="rounded-2xl border border-violet-100/80 bg-violet-50/40 p-5 ring-1 ring-violet-100/60">
                        <h3 class="text-sm font-black text-violet-950">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-[13px] leading-relaxed text-violet-900/85">{{ $item['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Keşif özellikleri --}}
        <section aria-labelledby="kesif">
            <h2 id="kesif" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Keşfet ve izle') }}
            </h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @php
                    $features = [
                        [
                            'route' => 'feed.index',
                            'label' => __('Canlı akış'),
                            'desc' => __('Tüm yayınlanmış bildirimler; il, ilçe, kategori ve arama ile filtreleyin.'),
                        ],
                        [
                            'route' => 'cities.explore',
                            'label' => __('Şehrini keşfet'),
                            'desc' => __('81 ildeki bildirim ve kampanyalara göz atın.'),
                        ],
                        [
                            'route' => 'campaigns.index',
                            'label' => __('Kampanyalar'),
                            'desc' => __('Toplumsal çağrılar; destekçi sayısı ve yorumlarla büyür.'),
                        ],
                        [
                            'route' => 'blog.index',
                            'label' => __('Blog'),
                            'desc' => __('Kent yaşamı, haklar ve platform hakkında yazılar.'),
                        ],
                    ];
                @endphp
                @foreach ($features as $feature)
                    <a href="{{ route($feature['route']) }}"
                        class="group rounded-2xl border border-neutral-200 bg-white p-5 transition hover:border-violet-200 hover:shadow-md hover:shadow-violet-500/5 ring-1 ring-black/[0.03]">
                        <span class="font-heading text-sm font-black text-neutral-900 group-hover:text-violet-700">{{ $feature['label'] }}</span>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-neutral-600">{{ $feature['desc'] }}</p>
                    </a>
                @endforeach
            </div>
            <p class="mt-5 text-[13px] leading-relaxed text-neutral-600">
                {{ __('Kısa görsel «hikâyeler» ana sayfa ve akışta yer alır; konum izni verirseniz yakınınızdaki kayıtlar öne çıkar. Kurum sayfalarında belediye veya şirkete ait bildirimler listelenir.') }}
            </p>
        </section>

        {{-- Kategoriler --}}
        <section aria-labelledby="kategoriler">
            <h2 id="kategoriler" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Bildirim kategorileri') }}
            </h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (['Altyapı', 'Ulaşım', 'Çevre ve Temizlik', 'Gürültü', 'Güvenlik'] as $catName)
                    <span
                        class="inline-flex rounded-full bg-neutral-100 px-3.5 py-1.5 text-[12px] font-bold text-neutral-800 ring-1 ring-neutral-200/80">{{ __($catName) }}</span>
                @endforeach
            </div>
        </section>

        {{-- Kurumlar --}}
        <section aria-labelledby="kurumlar"
            class="rounded-2xl border border-neutral-200 bg-gradient-to-br from-neutral-50 to-white p-6 sm:p-8 ring-1 ring-black/[0.04]">
            <h2 id="kurumlar" class="font-heading text-xl font-black tracking-tight text-neutral-900">
                {{ __('Belediye ve kurumlar için') }}
            </h2>
            <p class="mt-3 text-[14px] leading-relaxed text-neutral-700">
                {{ __('Kurum hesapları /brand adresinden giriş yapar. Kurum profil sayfalarında logo, şehir ve ilgili bildirimler görünür. Kurumsal yanıt ve panel özellikleri geliştirilmeye devam etmektedir.') }}
            </p>
            <a href="{{ route('login.brand') }}"
                class="mt-5 inline-flex rounded-full border border-neutral-300 bg-white px-4 py-2 text-[13px] font-bold text-neutral-800 hover:border-primary/30 hover:text-primary">{{ __('Kurum girişi (/brand)') }}</a>
        </section>

        {{-- SSS --}}
        <section aria-labelledby="sss">
            <h2 id="sss" class="font-heading text-xl font-black tracking-tight text-neutral-900 sm:text-2xl">
                {{ __('Sık sorulan sorular') }}
            </h2>
            <div class="mt-6 divide-y divide-neutral-200 rounded-2xl border border-neutral-200 bg-white ring-1 ring-black/[0.03]">
                @foreach ([
                    [
                        'q' => __('Bu platform resmi başvuru yerine geçer mi?'),
                        'a' => __('Hayır. Yasal süreçler ve resmî şikâyetler için ilgili kurumun kendi kanallarını (e-Devlet, çağrı merkezi, dilekçe vb.) kullanmalısınız. Buradaki amaç sorunu görünür kılmak ve topluluk desteği sağlamaktır.'),
                    ],
                    [
                        'q' => __('Bildirimim ne zaman yayınlanır?'),
                        'a' => __('Gönderimden sonra moderasyon ekibi içeriği inceler. Onaylanan kayıtlar akışta ve aramada görünür; reddedilenler size bildirilir.'),
                    ],
                    [
                        'q' => __('Kampanya nasıl başlatılır?'),
                        'a' => __('Üye olup telefonunuzu doğruladıktan sonra «Kampanya başlat» ile toplumsal bir çağrı oluşturabilirsiniz. Kampanyalar da moderasyondan geçer.'),
                    ],
                    [
                        'q' => __('Verilerim nasıl korunur?'),
                        'a' => __('Kişisel verileriniz KVKK ve gizlilik politikamız çerçevesinde işlenir. Ayrıntılar için yasal sayfalarımıza bakın.'),
                    ],
                ] as $faq)
                    <details class="group px-5 py-4 first:rounded-t-2xl last:rounded-b-2xl">
                        <summary
                            class="cursor-pointer list-none font-semibold text-neutral-900 marker:hidden [&::-webkit-details-marker]:hidden">
                            <span class="flex items-center justify-between gap-3">
                                {{ $faq['q'] }}
                                <svg class="h-5 w-5 shrink-0 text-neutral-400 transition group-open:rotate-180" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </span>
                        </summary>
                        <p class="mt-3 text-[13px] leading-relaxed text-neutral-600">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
            <p class="mt-4 text-[13px] text-neutral-600">
                {{ __('Başka sorularınız için') }}
                <a href="{{ route('contact') }}" class="font-semibold text-violet-700 underline-offset-4 hover:underline">{{ __('iletişim formunu') }}</a>
                {{ __('kullanabilirsiniz.') }}
            </p>
        </section>

        {{-- CTA --}}
        <section
            class="rounded-3xl bg-gradient-to-br from-violet-600 to-violet-800 px-6 py-10 text-center text-white shadow-xl shadow-violet-900/25 sm:px-10">
            <h2 class="font-heading text-xl font-black sm:text-2xl">{{ __('Hazır mısınız?') }}</h2>
            <p class="mx-auto mt-3 max-w-md text-[14px] leading-relaxed text-violet-100">
                {{ __('İlk bildiriminizi birkaç dakikada oluşturun veya şehrinizde neler konuşulduğuna bakın.') }}
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('posts.create') }}"
                    class="inline-flex items-center gap-1 rounded-full bg-white px-5 py-2.5 text-[13px] font-black text-violet-800 shadow-sm hover:bg-violet-50">
                    <span aria-hidden="true">+</span> {{ __('Kent sorunu bildir') }}
                </a>
                <a href="{{ route('cities.explore') }}"
                    class="inline-flex rounded-full border border-white/40 px-5 py-2.5 text-[13px] font-bold text-white hover:bg-white/10">
                    {{ __('Şehrini keşfet') }}
                </a>
            </div>
        </section>

        <p class="text-center text-[12px] leading-relaxed text-neutral-500">
            {{ __('Resmi başvuru yollarının yerini almaz. Yasal haklarınızı etkileyebilecek hususlarda mevzuat ve ilgili kurumları yanınıza alınız.') }}
            <a href="{{ route('legal.terms') }}" class="font-semibold underline-offset-2 hover:underline">{{ __('Kullanım koşulları') }}</a>
            ·
            <a href="{{ route('legal.privacy') }}" class="font-semibold underline-offset-2 hover:underline">{{ __('Gizlilik') }}</a>
        </p>
    </div>
@endsection
