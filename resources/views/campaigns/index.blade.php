@extends('layouts.app')

@section('title', __('Sosyal sorumluluk kampanyaları'))

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4 border-b border-neutral-200 pb-4">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-indigo-700">{{ __('Toplumsal dayanışma') }}</p>
            <h1 class="mt-2 text-[clamp(1.5rem,3vw,2.1rem)] font-black tracking-tight text-neutral-950">{{ __('Kampanyalar') }}</h1>
            <p class="mt-2 max-w-xl text-[14px] font-medium text-neutral-600">{{ __('Kullanıcılar kampanya başlatır; süper yönetici yayına uygun bulduğu projeler herkese açılır. Destek tek tıkla kayıtlı kullanıcılarla bağlanır.') }}</p>
        </div>
        @auth
            <a href="{{ route('campaigns.create') }}"
                class="inline-flex items-center rounded-full bg-indigo-600 px-6 py-3 text-[13px] font-black uppercase tracking-wide text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700">{{ __('Kampanya başlat') }}</a>
        @else
            <a href="{{ route('register') }}"
                class="inline-flex items-center rounded-full border-2 border-indigo-200 bg-white px-6 py-3 text-[13px] font-black text-indigo-900 hover:bg-indigo-50">{{ __('Katıl • kampanya aç') }}</a>
        @endauth
    </div>

    @if ($topics->isNotEmpty())
        <section class="mb-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-neutral-100">
            <h2 class="text-lg font-black text-neutral-950">{{ __('Konular') }}</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('campaigns.index', request()->only('city_id')) }}"
                    class="rounded-full border px-3 py-1.5 text-xs font-bold transition {{ empty($activeTopicFilter) ? 'border-sky-600 bg-sky-50 text-sky-900' : 'border-sky-200/80 bg-white text-sky-950 hover:bg-sky-50' }}">
                    {{ __('Tümü') }}
                </a>
                @foreach ($topics as $topic)
                    <a href="{{ route('campaigns.index', array_filter(['konu' => $topic->id, 'city_id' => $activeCityFilter ?: null])) }}"
                        class="rounded-full border px-3 py-1.5 text-xs font-bold transition {{ (int) $activeTopicFilter === (int) $topic->id ? 'border-sky-600 bg-sky-50 text-sky-900' : 'border-sky-200/80 bg-white text-sky-950 hover:bg-sky-50' }}">
                        {{ $topic->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <form method="get" action="{{ route('campaigns.index') }}" class="mb-8 flex flex-wrap items-end gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-neutral-100">
        @if ($activeTopicFilter)
            <input type="hidden" name="konu" value="{{ $activeTopicFilter }}">
        @endif
        <div class="min-w-[10rem] flex-1">
            <label class="text-[11px] font-bold uppercase tracking-wide text-neutral-500" for="cmp-city">{{ __('İl (isteğe bağlı)') }}</label>
            <select id="cmp-city" name="city_id"
                class="mt-1.5 w-full rounded-xl border-0 bg-[#eef0f3] px-4 py-3 text-sm font-bold text-neutral-900 shadow-inner outline-none ring-2 ring-transparent focus:ring-indigo-500">
                <option value="">{{ __('Tüm iller + genel') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}" @selected((int) ($activeCityFilter ?? 0) === (int) $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="rounded-xl bg-neutral-950 px-6 py-3 text-[12px] font-black uppercase tracking-wider text-white hover:bg-neutral-900">{{ __('Uygula') }}</button>
    </form>

    @if ($campaigns->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 py-24 text-center">
            <p class="text-[17px] font-bold text-indigo-950">{{ __('Bu filtre ile kampanya görünmüyor.') }}</p>
            <p class="mt-2 text-sm font-medium text-indigo-800/85">{{ __('Yeni kampanyalar onay sürecinden geçtikçe burada görünecek.') }}</p>
            @guest
                <a href="{{ route('register') }}" class="mt-6 inline-flex rounded-full bg-indigo-600 px-6 py-3 text-[13px] font-bold text-white">{{ __('Aramıza katıl') }}</a>
            @endguest
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($campaigns as $campaign)
                <article
                    class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-[0_2px_10px_-4px_rgba(76,61,207,0.35)] ring-2 ring-indigo-100 transition hover:-translate-y-0.5 hover:shadow-lg">
                    @if ($campaign->hero_image_url)
                        <a href="{{ route('campaigns.show', $campaign) }}" class="-mx-px -mt-px block">
                            <img src="{{ $campaign->hero_image_url }}" alt="" class="aspect-[2.2/1] w-full object-cover" loading="lazy">
                        </a>
                    @endif
                    <div class="flex flex-1 flex-col p-5">
                        @if ($campaign->topic)
                            <p class="text-[11px] font-black uppercase tracking-wider text-sky-800">{{ $campaign->topic->name }}</p>
                        @else
                            <p class="text-[11px] font-black uppercase tracking-wider text-indigo-700">{{ __('SSR kampanyası') }}</p>
                        @endif
                        <h2 class="mt-1 text-[1.125rem] font-black leading-snug tracking-tight text-neutral-950">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="hover:text-indigo-800">{{ $campaign->title }}</a></h2>
                        <p class="mt-2 line-clamp-3 flex-1 text-[13px] font-medium leading-relaxed text-neutral-700">
                            {{ \Illuminate\Support\Str::limit(strip_tags((string) ($campaign->excerpt ?? $campaign->description)), 220) }}</p>
                        <div class="mt-4 flex flex-wrap items-center gap-3 text-[12px] font-bold uppercase tracking-wide text-neutral-800">
                            <span>❤ {{ number_format(max(0, (int) $campaign->supporter_count)) }} {{ __('destek') }}</span>
                            @if ($campaign->goal_supporters)
                                <span>/ {{ __('hedef :n', ['n' => number_format((int) $campaign->goal_supporters)]) }}</span>
                            @endif
                            @if ($campaign->city)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-900">{{ $campaign->city->name }}</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-[11px] font-bold text-neutral-700">{{ __('genel Türkiye') }}</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8 flex justify-center">{{ $campaigns->links() }}</div>
    @endif
@endsection
