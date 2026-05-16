@extends('layouts.app')

@section('title', __('İletişim').' • '.config('app.name'))

@php
    $metricLabels = config('contact.metric_labels', []);
@endphp

@section('content')
    <div class="mx-auto max-w-[1220px] px-4 pb-20 pt-6 sm:px-5 lg:pt-10">
        <header class="relative overflow-hidden rounded-3xl border border-neutral-200/80 bg-gradient-to-br from-[#f4fbf8] via-white to-[#f2f5ff] p-8 shadow-xl shadow-neutral-900/5 sm:p-10 lg:p-12">
            <div class="pointer-events-none absolute -right-24 -top-28 h-72 w-72 rounded-full bg-emerald-400/15 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-20 -left-16 h-64 w-64 rounded-full bg-violet-500/10 blur-3xl" aria-hidden="true"></div>
            <div class="relative">
                <p class="inline-flex rounded-full bg-white/90 px-3 py-1 text-[11px] font-black uppercase tracking-widest text-emerald-700 ring-1 ring-emerald-200/80">{{ __('Kent & kampanya iletişim merkezi') }}</p>
                <h1 class="mt-4 max-w-3xl text-balance text-3xl font-black tracking-tight text-neutral-950 sm:text-4xl lg:text-[2.35rem]">
                    {{ __('BildirFIX ile doğrudan bağlantı kurun') }}
                </h1>
                <p class="mt-5 max-w-2xl leading-relaxed text-neutral-600">{{ config('contact.intro_highlight') }}</p>
                <dl class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach ($metricLabels as $card)
                        @php
                            $key = $card['key'];
                            $n = isset($counts[$key]) ? (int) $counts[$key] : 0;
                        @endphp
                        <div class="rounded-2xl border border-white/70 bg-white/80 p-5 shadow-inner shadow-neutral-900/[0.04] backdrop-blur">
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">{{ __(data_get($card, 'label')) }}</dt>
                            <dd class="mt-2 bg-gradient-to-r {{ data_get($card, 'accent', 'from-neutral-700 to-neutral-900') }} bg-clip-text text-3xl font-black tabular-nums text-transparent sm:text-[2rem]">
                                {{ number_format($n, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </header>

        <div class="mt-10 grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="space-y-10 lg:col-span-7">
                <section aria-labelledby="sec-campaigns" class="rounded-3xl border border-neutral-200/90 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 id="sec-campaigns" class="text-xl font-black text-neutral-900">{{ __('Öne çıkan kampanyalar') }}</h2>
                            <p class="mt-1 text-sm leading-relaxed text-neutral-500">{{ __('Onaylı, topluluktan destek toplayan aktiviteler (canlı veri).') }}</p>
                        </div>
                        <a href="{{ route('campaigns.index') }}" class="text-sm font-bold text-[#6C5CE7] underline-offset-4 hover:underline">{{ __('Tüm kampanyalar') }}</a>
                    </div>
                    @if ($spotlightCampaigns->isEmpty())
                        <p class="mt-6 text-sm text-neutral-500">{{ __('Henüz listelenecek onaylı kampanya yok.') }}</p>
                    @else
                        <ul class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($spotlightCampaigns as $campaign)
                                <li>
                                    <a href="{{ route('campaigns.show', $campaign) }}"
                                        class="group flex h-full flex-col rounded-2xl border border-neutral-100 bg-neutral-50/40 shadow-sm transition hover:-translate-y-0.5 hover:border-[#d4ceff] hover:shadow-[0_12px_32px_-12px_rgba(108,92,231,.25)]">
                                        <div class="relative aspect-[16/10] overflow-hidden rounded-t-2xl bg-gradient-to-br from-neutral-100 to-neutral-200">
                                            @if ($campaign->hero_image_url)
                                                <img src="{{ $campaign->hero_image_url }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#6C5CE7]/90 to-teal-600/85 text-[11px] font-black uppercase tracking-wider text-white/95">
                                                    {{ __('Kampanya') }}
                                                </div>
                                            @endif
                                            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-neutral-700 shadow-sm">
                                                {{ number_format(max(0, (int) $campaign->supporter_count), 0, ',', '.') }} {{ __('destekçi') }}
                                            </span>
                                        </div>
                                        <div class="flex flex-1 flex-col p-4">
                                            <p class="line-clamp-2 text-[15px] font-bold leading-snug text-neutral-900 group-hover:text-[#5b4dcf]">{{ $campaign->title }}</p>
                                            <p class="mt-2 line-clamp-2 flex-1 text-xs leading-relaxed text-neutral-500">
                                                {{ $campaign->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) $campaign->description), 120) }}
                                            </p>
                                            <span class="mt-3 inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700">
                                                {{ __('Detay →') }}
                                            </span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section aria-labelledby="sec-issues" class="rounded-3xl border border-neutral-200/90 bg-white p-6 shadow-sm sm:p-8">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <h2 id="sec-issues" class="text-xl font-black text-neutral-900">{{ __('Son kent bildirimleri') }}</h2>
                            <p class="mt-1 text-sm text-neutral-500">{{ __('Farklı kategoriler ve şehirlerden örnek akış.') }}</p>
                        </div>
                        <a href="{{ route('home') }}#liste-sikayetleri" class="text-sm font-bold text-emerald-800 underline-offset-4 hover:underline">{{ __('Akışı aç') }}</a>
                    </div>
                    @if ($recentComplaints->isEmpty())
                        <p class="mt-6 text-sm text-neutral-500">{{ __('Liste boş.') }}</p>
                    @else
                        <ul class="mt-6 divide-y divide-neutral-100">
                            @foreach ($recentComplaints as $post)
                                <li class="flex flex-wrap gap-3 py-4 first:pt-0">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('posts.show', $post) }}" class="font-bold text-neutral-900 hover:text-[#6C5CE7]">{{ $post->title }}</a>
                                        <div class="mt-1 flex flex-wrap gap-2 text-[11px] font-semibold text-neutral-500">
                                            @if ($post->category)
                                                <span class="rounded-full bg-violet-50 px-2 py-0.5 text-violet-800">{{ $post->category->name }}</span>
                                            @endif
                                            @if ($post->city)
                                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-900">{{ $post->city->name }}</span>
                                            @endif
                                            @if ($post->district)
                                                <span class="text-neutral-400">{{ $post->district->name }}</span>
                                            @endif
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <span class="shrink-0 self-start rounded-full border border-neutral-200 bg-neutral-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-neutral-600">{{ $post->status->label() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                @php
                    $channels = config('contact.channels', []);
                    $departments = config('contact.departments', []);
                    $faq = config('contact.faq', []);
                    $sla = config('contact.response_sla', []);
                @endphp

                <section aria-labelledby="sec-channels" class="rounded-3xl border border-neutral-200/90 bg-neutral-900 p-6 text-neutral-50 sm:p-8">
                    <h2 id="sec-channels" class="text-lg font-black tracking-tight text-white">{{ __('İletişim kanalları') }}</h2>
                    <ul class="mt-6 grid gap-4 sm:grid-cols-2">
                        @foreach ($channels as $ch)
                            <li class="rounded-2xl border border-white/10 bg-white/[0.05] p-4">
                                <p class="text-[11px] font-black uppercase tracking-wider text-teal-200/90">{{ __(data_get($ch, 'label')) }}</p>
                                <p class="mt-2 break-all text-sm font-bold text-white">{{ data_get($ch, 'value') }}</p>
                                @if ($note = data_get($ch, 'note'))
                                    <p class="mt-2 text-xs leading-relaxed text-neutral-400">{{ __( $note ) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section aria-labelledby="sec-depts" class="rounded-3xl border border-neutral-200/90 bg-white p-6 shadow-sm sm:p-8">
                    <h2 id="sec-depts" class="text-lg font-black text-neutral-900">{{ __('Birim özetleri') }}</h2>
                    <div class="mt-6 grid gap-5 md:grid-cols-3">
                        @foreach ($departments as $dept)
                            <article class="flex flex-col rounded-2xl border border-neutral-100 bg-neutral-50/60 p-5">
                                <h3 class="text-[15px] font-bold text-neutral-900">{{ __(data_get($dept, 'title')) }}</h3>
                                <p class="mt-2 text-xs leading-relaxed text-neutral-600">{{ __(data_get($dept, 'summary')) }}</p>
                                @if (($bullets = data_get($dept, 'bullets')) !== null && count((array) $bullets))
                                    <ul class="mt-4 list-disc space-y-1 pl-4 text-[11px] font-medium text-neutral-500">
                                        @foreach ((array) $bullets as $b)
                                            <li>{{ __($b) }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>

                <section aria-labelledby="sec-sla" class="rounded-3xl border border-dashed border-emerald-200/90 bg-emerald-50/40 px-6 py-6 sm:px-8">
                    <h2 id="sec-sla" class="text-lg font-black text-emerald-950">{{ __('Tipik geri dönüş süreleri') }}</h2>
                    <p class="mt-2 text-xs text-emerald-900/70">{{ __('Rehber sürelerdir; yoğunluk ve karmaşıklığa göre değişir.') }}</p>
                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        @foreach ($sla as $row)
                            <div class="flex justify-between gap-3 rounded-xl border border-emerald-100/70 bg-white/70 px-4 py-3 text-sm">
                                <dt class="font-semibold text-neutral-700">{{ __(data_get($row, 'label')) }}</dt>
                                <dd class="shrink-0 text-right font-bold text-emerald-800">{{ __(data_get($row, 'value')) }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section aria-labelledby="sec-faq" class="rounded-3xl border border-neutral-200/90 bg-white p-6 sm:p-8">
                    <h2 id="sec-faq" class="text-lg font-black text-neutral-900">{{ __('Sık sorulanlar') }}</h2>
                    <div class="mt-6 space-y-3">
                        @foreach ($faq as $item)
                            <details class="group rounded-2xl border border-neutral-100 bg-neutral-50/70 px-4 py-3 open:bg-white">
                                <summary class="cursor-pointer list-none text-sm font-bold text-neutral-900 marker:content-none [&::-webkit-details-marker]:hidden">{{ __(data_get($item, 'q')) }}</summary>
                                <p class="mt-3 pb-1 text-xs leading-relaxed text-neutral-600">{{ __(data_get($item, 'a')) }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-[5.75rem]">
                    <article class="rounded-3xl border border-teal-100/80 bg-white/95 p-6 shadow-xl shadow-teal-500/10 ring-1 ring-teal-50 sm:p-8">
                        <h2 class="text-xl font-black tracking-tight text-teal-950">{{ __('Mesaj bırakın') }}</h2>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600">
                            {{ __('Ad, e-posta ve konunu seçtikten sonra iletin; kayıtlar günlük olarak incelenir.') }}
                        </p>

                        <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                            @csrf
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wide text-teal-900/75">{{ __('Adınız') }}</label>
                                <input name="name" value="{{ old('name') }}" required maxlength="120"
                                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-semibold shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
                                @error('name')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wide text-teal-900/75">{{ __('E-posta') }}</label>
                                <input name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-semibold shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
                                @error('email')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wide text-teal-900/75">{{ __('Konu') }}</label>
                                <select name="topic"
                                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm font-semibold shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">
                                    @foreach (config('contact.form_topics', []) as $topicRow)
                                        <option value="{{ data_get($topicRow, 'value') }}" @selected(old('topic', '') === data_get($topicRow, 'value'))>{{ __(data_get($topicRow, 'label')) }}</option>
                                    @endforeach
                                </select>
                                @error('topic')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-wide text-teal-900/75">{{ __('Mesaj') }}</label>
                                <textarea name="message" rows="8" maxlength="4000" required
                                    class="mt-2 w-full rounded-2xl border border-slate-100 bg-white px-4 py-3 text-sm leading-relaxed shadow-inner focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-400/30">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-turnstile-widget class="rounded-2xl border border-dashed border-slate-100 bg-slate-50 px-4 py-3" />

                            <button type="submit"
                                class="w-full rounded-2xl bg-gradient-to-r from-teal-600 to-teal-800 px-4 py-3.5 text-sm font-black uppercase tracking-wide text-white shadow-lg shadow-teal-700/35 hover:from-teal-700 hover:to-teal-950">
                                {{ __('Gönder') }}
                            </button>
                        </form>
                    </article>
                </div>
            </div>
        </div>
    </div>
@endsection
