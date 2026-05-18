@extends('layouts.app')

@section('title', __('Şehrini keşfet').' • '.config('app.name'))

@section('content')
    <div class="space-y-8" x-data="{
        q: '',
        mode: 'region',
        cities: {{ \Illuminate\Support\Js::from($citiesJson) }},
        regionOrder: {{ \Illuminate\Support\Js::from($regionOrder) }},
        regionLabels: {{ \Illuminate\Support\Js::from($regionLabels) }},
        trFold(v) {
            return (v || '').toLocaleLowerCase('tr-TR').normalize('NFC');
        },
        get filtered() {
            const s = this.trFold((this.q || '').trim());
            if (!s) return this.cities;
            return this.cities.filter((c) => {
                const name = this.trFold(c.name);
                const slug = this.trFold(c.slug);
                const plate = c.plate != null ? String(c.plate) : '';
                return name.includes(s) || slug.includes(s) || plate.includes(s);
            });
        },
        get grouped() {
            const out = {};
            this.regionOrder.forEach((k) => { out[k] = []; });
            this.filtered.forEach((c) => {
                const k = c.region || 'diger';
                if (!out[k]) out[k] = [];
                out[k].push(c);
            });
            Object.keys(out).forEach((k) => {
                out[k].sort((a, b) => a.name.localeCompare(b.name, 'tr'));
            });
            return out;
        },
        get alphaBlocks() {
            const buckets = {};
            this.filtered.forEach((c) => {
                const ch = Array.from(c.name || '')[0] || '#';
                const letter = ch.toLocaleUpperCase('tr-TR');
                if (!buckets[letter]) buckets[letter] = [];
                buckets[letter].push(c);
            });
            return Object.keys(buckets)
                .sort((a, b) => a.localeCompare(b, 'tr'))
                .map((letter) => ({
                    letter,
                    cities: buckets[letter].sort((a, b) => a.name.localeCompare(b.name, 'tr')),
                }));
        },
    }">
        <header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] sm:p-8">
            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-primary">{{ __('Keşfet') }}</p>
            <h1 class="mt-2 text-[clamp(1.5rem,3vw,2.25rem)] font-black tracking-tight text-neutral-950">{{ __('Şehrini keşfet') }}</h1>
            <p class="mt-3 max-w-2xl text-[15px] font-medium leading-relaxed text-neutral-600">
                {{ __('İlini seç: yalnızca o ile ait onaylı şikâyet kayıtlarını listeleriz. Listeyi bölgeye veya A’dan Z’ye göre düzenleyebilir, arama kutusuyla daraltabilirsin.') }}
            </p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                <label class="relative block min-w-[min(100%,18rem)] flex-1">
                    <span class="sr-only">{{ __('İl ara') }}</span>
                    <input type="search" x-model="q" autocomplete="off"
                        class="input-ds w-full rounded-2xl px-4 py-3 text-sm font-semibold"
                        placeholder="{{ __('İl veya plaka ara…') }}">
                </label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="mode = 'region'"
                        :class="mode === 'region' ? 'bg-primary text-white ring-primary' : 'bg-white text-neutral-800 ring-neutral-200'"
                        class="rounded-full px-4 py-2 text-xs font-bold ring-1 transition hover:opacity-95">
                        {{ __('Bölgelere göre') }}
                    </button>
                    <button type="button" @click="mode = 'alpha'"
                        :class="mode === 'alpha' ? 'bg-primary text-white ring-primary' : 'bg-white text-neutral-800 ring-neutral-200'"
                        class="rounded-full px-4 py-2 text-xs font-bold ring-1 transition hover:opacity-95">
                        {{ __('A’dan Z’ye') }}
                    </button>
                </div>
            </div>
            <p class="mt-4 text-xs font-medium text-neutral-500">
                <a href="{{ route('home') }}" class="font-bold text-primary underline-offset-2 hover:underline">{{ __('Ana sayfa') }}</a>
                · {{ __('Tüm Türkiye özetini burada görmeye devam edebilirsin.') }}
            </p>
        </header>

        <div class="rounded-2xl border border-dashed border-neutral-200 bg-neutral-50/80 px-4 py-10 text-center text-sm font-semibold text-neutral-600"
            x-show="filtered.length === 0" x-cloak>
            {{ __('Eşleşen il bulunamadı.') }}
        </div>

        {{-- Bölgelere göre --}}
        <div class="space-y-10" x-show="mode === 'region' && filtered.length" x-cloak>
            <template x-for="regionKey in regionOrder" :key="regionKey">
                <section x-show="(grouped[regionKey] || []).length" class="scroll-mt-24">
                    <h2 class="mb-4 flex flex-wrap items-baseline gap-2 border-b border-neutral-200 pb-2 text-base font-black text-neutral-900">
                        <span class="sr-only">{{ __('Bölge') }}:</span>
                        <span x-text="regionLabels[regionKey] || regionKey"></span>
                    </h2>
                    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="c in (grouped[regionKey] || [])" :key="c.id">
                            <li>
                                <a :href="c.url"
                                    class="flex items-center justify-between gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm ring-1 ring-black/[0.02] transition hover:border-primary/35 hover:ring-primary/15">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-extrabold text-neutral-900" x-text="c.name"></span>
                                        <span class="mt-0.5 text-[11px] font-semibold text-neutral-500" x-text="c.plate ? ('Plaka ' + c.plate) : ''"></span>
                                    </span>
                                    <span class="shrink-0 rounded-full bg-neutral-100 px-2.5 py-1 text-[11px] font-black tabular-nums text-neutral-800"
                                        x-text="c.count"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </section>
            </template>
        </div>

        {{-- Alfabetik --}}
        <div class="space-y-10" x-show="mode === 'alpha' && filtered.length" x-cloak>
            <template x-for="block in alphaBlocks" :key="block.letter">
                <section class="scroll-mt-24">
                    <h2 class="mb-4 w-min rounded-xl bg-neutral-900 px-3 py-1 text-sm font-black text-white">
                        <span class="sr-only">{{ __('Alfabe') }} </span>
                        <span x-text="block.letter"></span>
                    </h2>
                    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="c in block.cities" :key="c.id">
                            <li>
                                <a :href="c.url"
                                    class="flex items-center justify-between gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm ring-1 ring-black/[0.02] transition hover:border-primary/35 hover:ring-primary/15">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-extrabold text-neutral-900" x-text="c.name"></span>
                                        <span class="mt-0.5 text-[11px] font-semibold text-neutral-500" x-text="c.plate ? ('Plaka ' + c.plate) : ''"></span>
                                    </span>
                                    <span class="shrink-0 rounded-full bg-neutral-100 px-2.5 py-1 text-[11px] font-black tabular-nums text-neutral-800"
                                        x-text="c.count"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </section>
            </template>
        </div>
    </div>
@endsection
