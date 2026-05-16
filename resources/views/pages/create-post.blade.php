@extends('layouts.app')

@section('title', __('Kent bildir').' • '.config('app.name'))

@php
    $d = $complaintDraft ?? [];
    $wizardStep = 1;
    if ($errors->any()) {
        $keys = $errors->keys();
        $match = static function (array $fields) use ($keys): bool {
            foreach ($keys as $key) {
                foreach ($fields as $f) {
                    if ($key === $f || str_starts_with((string) $key, $f.'.')) {
                        return true;
                    }
                }
            }

            return false;
        };
        if ($match(['description', 'category_id', 'images', 'videos', 'media_url', 'institution_ids'])) {
            $wizardStep = 1;
        } elseif ($match(['city_id', 'district_id', 'neighborhood_turkiye_id', 'neighborhood_name', 'latitude', 'longitude'])) {
            $wizardStep = 2;
        } elseif ($match(['title'])) {
            $wizardStep = 3;
        }
    }
@endphp

@section('content')
    <div class="mx-auto max-w-[1200px] px-3 pb-24 pt-3 sm:px-5 sm:pb-14 sm:pt-5"
        x-data="dsQuickComplaint({
            wizardStep: @js($wizardStep),
            categoryId: @js(old('category_id', $d['category_id'] ?? '')),
            cityId: @js((string) old('city_id', $cityId)),
            districtId: @js((string) old('district_id', $d['district_id'] ?? '')),
            neighborhoodTurkiyeId: @js((string) old('neighborhood_turkiye_id', $d['neighborhood_turkiye_id'] ?? '')),
            neighborhoodName: @js(old('neighborhood_name', $d['neighborhood_name'] ?? '')),
            latitude: @js(old('latitude', $d['latitude'] ?? '')),
            longitude: @js(old('longitude', $d['longitude'] ?? '')),
            selectedInstitutions: @js($selectedInstitutions->map(fn ($i) => ['id' => (int) $i->id, 'name' => $i->name])->values()->all()),
            urls: {
                districts: @js(route('geo.districts')),
                neighborhoods: @js(route('geo.neighborhoods')),
                institutions: @js(route('geo.institutions')),
            },
        })"
        x-init="initQuick()">

        <div class="mb-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('home') }}"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm transition hover:border-primary/30 hover:bg-primary-light/50">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                <span class="sr-only">{{ __('Ana sayfa') }}</span>
            </a>
            <div class="min-w-0">
                <h1 class="font-heading text-[clamp(1.35rem,4vw,1.85rem)] font-black tracking-tight text-neutral-900">
                    {{ __('Kent bildir') }}</h1>
                <p class="mt-1 text-sm font-medium text-neutral-500">{{ __('Üç adımda paylaşımını oluştur: konu ve muhataplar, konum, özet.') }}</p>
            </div>
        </div>

        @php
            $geoHint = \App\Models\District::query()->whereNotNull('turkiye_id')->doesntExist();
        @endphp
        @if ($geoHint)
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[13px] font-semibold text-amber-950">
                {{ __('Tam il/ilçe listesi için sunucuda şu komutu çalıştırın:') }}
                <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan turkiye:sync-geo</code>
            </div>
        @endif

        <div class="flex flex-col gap-8 lg:grid lg:grid-cols-12 lg:items-start lg:gap-10">
            {{-- Sol: nasıl oluşturulur (geniş ekranda sabit özet) --}}
            <aside class="order-2 space-y-4 lg:sticky lg:top-24 lg:order-1 lg:col-span-4 xl:col-span-4">
                <div
                    class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-[0_4px_24px_-12px_rgba(15,23,42,0.14)] ring-1 ring-black/[0.04] sm:p-6">
                    <h2 class="font-heading text-lg font-black tracking-tight text-neutral-900">{{ __('Nasıl oluşturulur?') }}</h2>
                    <p class="mt-2 text-[13px] font-medium leading-relaxed text-neutral-600">{{ __('Net bir bildirim hem kurumların hem komşuların işini kolaylaştırır.') }}</p>
                    <ol class="mt-5 space-y-4 text-[13px] font-semibold leading-snug text-neutral-800">
                        <li class="flex gap-3">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-black text-primary ring-1 ring-primary/20">1</span>
                            <span><span class="text-neutral-900">{{ __('Konuyu yazın') }}</span>
                                <span class="mt-0.5 block text-[12px] font-medium text-neutral-500">{{ __('Ne oldu, kategori; fotoğraf veya video ekleyebilirsiniz.') }}</span></span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-black text-primary ring-1 ring-primary/20">2</span>
                            <span><span class="text-neutral-900">{{ __('Konumu seçin') }}</span>
                                <span class="mt-0.5 block text-[12px] font-medium text-neutral-500">{{ __('İl, ilçe, mahalle ve isteğe bağlı tam konum.') }}</span></span>
                        </li>
                        <li class="flex gap-3">
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light text-sm font-black text-primary ring-1 ring-primary/20">3</span>
                            <span><span class="text-neutral-900">{{ __('Özet başlık ve gönder') }}</span>
                                <span class="mt-0.5 block text-[12px] font-medium text-neutral-500">{{ __('Listede görünecek kısa başlığı yazın.') }}</span></span>
                        </li>
                    </ol>
                    <div class="mt-5 rounded-xl border border-primary/15 bg-gradient-to-br from-primary-light to-orange-50 px-4 py-3 text-[12px] font-semibold text-neutral-900 ring-1 ring-primary/10">
                        {{ __('İpucu: mümkünse fotoğraf ekleyin; birden fazla dosya seçebilir veya sürükleyip bırakabilirsiniz.') }}
                    </div>
                </div>
            </aside>

            {{-- Sağ: form --}}
            <div class="order-1 min-w-0 lg:order-2 lg:col-span-8 xl:col-span-8">
                <nav class="mb-5 grid grid-cols-3 gap-2 text-center text-[11px] font-black uppercase tracking-wide text-neutral-500 sm:text-xs"
                    aria-label="{{ __('Adımlar') }}">
                    <div class="rounded-xl border px-2 py-2 transition sm:px-3"
                        :class="wizardStep === 1 ? 'border-primary bg-primary-light text-primary ring-1 ring-primary/20' : 'border-neutral-200 bg-white'">
                        <span class="block text-[10px] font-bold opacity-70">1</span>
                        {{ __('Konu') }}</div>
                    <div class="rounded-xl border px-2 py-2 transition sm:px-3"
                        :class="wizardStep === 2 ? 'border-primary bg-primary-light text-primary ring-1 ring-primary/20' : 'border-neutral-200 bg-white'">
                        <span class="block text-[10px] font-bold opacity-70">2</span>
                        {{ __('Konum') }}</div>
                    <div class="rounded-xl border px-2 py-2 transition sm:px-3"
                        :class="wizardStep === 3 ? 'border-primary bg-primary-light text-primary ring-1 ring-primary/20' : 'border-neutral-200 bg-white'">
                        <span class="block text-[10px] font-bold opacity-70">3</span>
                        {{ __('Özet') }}</div>
                </nav>

                <div
                    class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-[0_4px_24px_-10px_rgba(15,23,42,0.18)] ring-1 ring-black/[0.04] sm:p-8">
            <p class="rounded-xl bg-gradient-to-r from-primary-light to-orange-50 px-4 py-3 text-[14px] font-semibold leading-snug text-neutral-900 ring-1 ring-primary/15">
                {{ __('En fazla :i foto (yaklaşık :f MB), en fazla :v adet mp4/webm videolar. İstersen bağlantı ile de ekleyebilirsin.', [
                    'i' => config('complaint.max_images'),
                    'v' => config('complaint.max_videos'),
                    'f' => (int) round(config('complaint.image_max_kb') / 1024),
                ]) }}
            </p>

            <form id="quick-complaint-form" class="mt-6 space-y-5" method="POST" action="{{ route('posts.store') }}"
                enctype="multipart/form-data" @submit="guardQuickComplaint($event)">
                @csrf
                <input type="hidden" name="category_id" x-bind:value="categoryId">
                <input type="hidden" name="latitude" x-bind:value="latitude !== '' && latitude !== null ? latitude : ''">
                <input type="hidden" name="longitude" x-bind:value="longitude !== '' && longitude !== null ? longitude : ''">

                <template x-for="row in selectedInstitutions" :key="row.id">
                    <input type="hidden" name="institution_ids[]" :value="row.id">
                </template>

                {{-- Adım 1: Konu, medya, muhatap kurumlar --}}
                <div x-show="wizardStep === 1" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 1">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500"
                            for="quick-description">{{ __('Ne oldu? Sorunu yazın') }} <span class="text-red-600">*</span></label>
                        <textarea id="quick-description" name="description" rows="7" maxlength="8000" required
                            class="input-ds min-h-[10rem] w-full resize-y rounded-xl border-neutral-200 text-[15px] leading-relaxed"
                            placeholder="{{ __('Ne yaşandı, nerede, ne zaman; beklentiniz…') }}">{{ old('description', $d['description'] ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('Kategori') }} <span class="text-red-600">*</span></p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $cat)
                                <button type="button"
                                    class="inline-flex min-h-[46px] items-center gap-2 rounded-full border-2 px-4 py-2 text-sm font-bold transition"
                                    @click="categoryId = '{{ $cat->id }}'"
                                    :class="categoryId === '{{ $cat->id }}'
                                        ? 'border-primary bg-primary-light text-primary shadow-sm ring-1 ring-primary/25'
                                        : 'border-neutral-100 bg-neutral-50 text-neutral-800 hover:border-neutral-200'">
                                    <span class="font-black text-primary">{{ mb_substr($cat->name, 0, 1) }}</span>
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                        @error('category_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('Fotoğraf') }}
                            <span class="font-normal normal-case text-neutral-400">{{ __('(:n — en fazla :m)', ['n' => config('complaint.max_images'), 'm' => (int) (config('complaint.image_max_kb') / 1024).' MB']) }}</span>
                        </p>
                        <input type="file" id="quick-images-files" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple
                            class="pointer-events-none fixed h-px w-px opacity-0" tabindex="-1" aria-hidden="true">
                        <div
                            class="rounded-xl border-2 border-dashed border-neutral-200 bg-neutral-50/40 px-4 py-5 transition sm:px-5"
                            :class="imagesDropActive ? 'border-primary bg-primary-light/40 ring-2 ring-primary/15' : ''"
                            @dragenter.prevent="imagesDropActive = true"
                            @dragover.prevent="imagesDropActive = true"
                            @dragleave.prevent="imagesDropLeave($event)"
                            @drop.prevent="onImagesDrop($event)">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-[13px] font-semibold text-neutral-700">{{ __('Dosya seçin veya buraya sürükleyin; önizleme hemen görünür.') }}</p>
                                <label for="quick-images-picker"
                                    class="inline-flex shrink-0 cursor-pointer items-center justify-center rounded-full border border-neutral-200 bg-white px-5 py-2.5 text-[13px] font-bold text-neutral-800 shadow-sm transition hover:border-primary/35 hover:bg-primary-light/40">
                                    {{ __('Fotoğraf ekle') }}
                                </label>
                            </div>
                            <input id="quick-images-picker" type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple class="sr-only"
                                @change="addImagesFromPicker($event)">
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" x-show="imagePreviewItems.length > 0" x-cloak>
                                <template x-for="item in imagePreviewItems" :key="item.key">
                                    <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-neutral-900/[0.03] shadow-sm">
                                        <img :src="item.url" alt="" class="aspect-square w-full object-cover">
                                        <button type="button"
                                            class="absolute right-1.5 top-1.5 flex h-8 w-8 items-center justify-center rounded-full bg-neutral-900/75 text-lg font-bold leading-none text-white shadow-md backdrop-blur-sm transition hover:bg-red-600"
                                            @click="removeImagePreview(item.key)" aria-label="{{ __('Kaldır') }}">×</button>
                                        <p class="truncate px-2 py-1.5 text-[11px] font-semibold text-neutral-600" x-text="item.file.name"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('images')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('Video (kısa)') }}
                            <span class="font-normal normal-case text-neutral-400">{{ __('(en fazla :n adet)', ['n' => config('complaint.max_videos')]) }}</span>
                        </p>
                        <input type="file" id="quick-videos-files" name="videos[]" accept="video/mp4,video/webm" multiple
                            class="pointer-events-none fixed h-px w-px opacity-0" tabindex="-1" aria-hidden="true">
                        <div
                            class="rounded-xl border-2 border-dashed border-neutral-200 bg-neutral-50/40 px-4 py-5 transition sm:px-5"
                            :class="videosDropActive ? 'border-primary bg-primary-light/40 ring-2 ring-primary/15' : ''"
                            @dragenter.prevent="videosDropActive = true"
                            @dragover.prevent="videosDropActive = true"
                            @dragleave.prevent="videosDropLeave($event)"
                            @drop.prevent="onVideosDrop($event)">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-[13px] font-semibold text-neutral-700">{{ __('Mp4 veya WebM; önizleme hemen oynatılır.') }}</p>
                                <label for="quick-videos-picker"
                                    class="inline-flex shrink-0 cursor-pointer items-center justify-center rounded-full border border-neutral-200 bg-white px-5 py-2.5 text-[13px] font-bold text-neutral-800 shadow-sm transition hover:border-primary/35 hover:bg-primary-light/40">
                                    {{ __('Video ekle') }}
                                </label>
                            </div>
                            <input id="quick-videos-picker" type="file" accept="video/mp4,video/webm" multiple class="sr-only"
                                @change="addVideosFromPicker($event)">
                            <div class="mt-4 grid gap-3 sm:grid-cols-2" x-show="videoPreviewItems.length > 0" x-cloak>
                                <template x-for="item in videoPreviewItems" :key="item.key">
                                    <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-black shadow-sm">
                                        <video :src="item.url" muted playsinline controls preload="metadata"
                                            class="max-h-52 w-full object-contain bg-black"></video>
                                        <button type="button"
                                            class="absolute right-1.5 top-1.5 flex h-8 w-8 items-center justify-center rounded-full bg-neutral-900/75 text-lg font-bold leading-none text-white shadow-md backdrop-blur-sm transition hover:bg-red-600"
                                            @click="removeVideoPreview(item.key)" aria-label="{{ __('Kaldır') }}">×</button>
                                        <p class="truncate bg-white px-2 py-1.5 text-[11px] font-semibold text-neutral-600" x-text="item.file.name"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('videos')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500" for="quick-media">{{ __('Medya bağlantısı') }}
                            <span class="font-normal normal-case text-neutral-400">({{ __('isteğe bağlı') }})</span></label>
                        <input id="quick-media" type="url" name="media_url" maxlength="2048"
                            value="{{ old('media_url', $d['media_url'] ?? '') }}"
                            class="input-ds min-h-[52px] w-full rounded-xl border-neutral-200 text-[15px]"
                            placeholder="https://…" autocomplete="off" @input.debounce.400ms="syncPreviewImg()">
                        @error('media_url')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="relative mt-3 overflow-hidden rounded-xl border border-neutral-100 bg-neutral-50">
                            <img id="quick-media-preview" src="" alt="" class="hidden max-h-56 w-full object-cover">
                            <div id="quick-media-empty"
                                class="flex flex-col items-center justify-center gap-2 px-4 py-10 text-center text-sm text-neutral-400">
                                <svg class="h-9 w-9 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.51-7.231 1.509-1.509a2.25 2.25 0 1 1 3.182 3.182l-7.038 7.038a4.5 4.5 0 0 1-6.364 0L3 12m9 9h3.75M9 21h6a2.25 2.25 0 0 0 2.25-2.25V9.75a2.25 2.25 0 0 0-2.25-2.25H9A2.25 2.25 0 0 0 6.75 9.75v9A2.25 2.25 0 0 0 9 21Z" />
                                </svg>
                                {{ __('Bağlantı girildiğinde küçük önizleme burada görünür.') }}</div>
                        </div>
                    </div>

                    <fieldset class="rounded-xl border border-neutral-200 bg-neutral-50/50 p-4 ring-1 ring-black/[0.02]">
                        <legend class="mb-2 px-1 text-xs font-bold uppercase tracking-wide text-neutral-500">
                            {{ __('Konunun muhatapları (kurum)') }}
                            <span class="font-normal normal-case text-neutral-400">{{ __('(isteğe bağlı; yazarak ara, birden fazla seç)') }}</span>
                        </legend>
                        <label class="sr-only" for="institution-search">{{ __('Kurum ara') }}</label>
                        <input id="institution-search" type="search" autocomplete="off"
                            class="input-ds mb-2 min-h-[48px] w-full rounded-xl border-neutral-200 text-[15px]"
                            placeholder="{{ __('Kurum adı yazın (en az 2 harf)…') }}"
                            x-model="institutionSearch"
                            @input.debounce.320ms="runInstitutionSearch()"
                            @keydown.escape.prevent="institutionHits = []">
                        <p class="mb-2 text-[12px] text-neutral-500">{{ __('İsterseniz 2. adımda ili seçtikten sonra aramayı daraltabilirsiniz; ilk adımda tüm kurumlar aranır.') }}</p>
                        <div class="relative">
                            <ul x-show="institutionHits.length > 0" x-transition
                                class="absolute z-20 mt-1 max-h-52 w-full overflow-auto rounded-xl border border-neutral-200 bg-white py-1 shadow-lg ring-1 ring-black/5">
                                <template x-for="hit in institutionHits" :key="hit.id">
                                    <li>
                                        <button type="button"
                                            class="flex w-full px-3 py-2.5 text-left text-[14px] font-medium text-neutral-900 hover:bg-primary-light/60"
                                            x-text="hit.name"
                                            @click="addInstitution(hit)"></button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <p class="mt-2 text-[12px] font-semibold text-neutral-500" x-show="institutionSearchLoading">{{ __('Aranıyor…') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2" x-show="selectedInstitutions.length > 0">
                            <template x-for="row in selectedInstitutions" :key="'chip-' + row.id">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-primary/25 bg-primary-light/80 px-3 py-1.5 text-[13px] font-bold text-primary">
                                    <span x-text="row.name" class="max-w-[14rem] truncate"></span>
                                    <button type="button" class="rounded-full p-0.5 hover:bg-white/80"
                                        @click="removeInstitution(row.id)" aria-label="{{ __('Kaldır') }}">&times;</button>
                                </span>
                            </template>
                        </div>
                        @error('institution_ids')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="flex justify-end pt-1">
                        <button type="button" class="btn-primary inline-flex min-h-[50px] items-center justify-center rounded-full px-8 text-[15px] font-bold shadow-cta"
                            @click="nextStep()">{{ __('İleri: konum') }}</button>
                    </div>
                </div>

                {{-- Adım 2: Adres / il-ilçe-mahalle + harita --}}
                <div x-show="wizardStep === 2" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500" for="quick-city">{{ __('İl') }}
                            <span class="text-red-600">*</span></label>
                        <select id="quick-city" name="city_id" required x-model="cityId"
                            @change="onCityChanged()"
                            class="input-ds min-h-[52px] w-full rounded-xl border-neutral-200 bg-neutral-50/50 text-[15px]">
                            @foreach ($cities as $c)
                                <option value="{{ $c->id }}" @selected((int) old('city_id', $cityId) === (int) $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500"
                            for="quick-district">{{ __('İlçe') }} <span class="text-red-600">*</span></label>
                        <select id="quick-district" name="district_id" required x-model="districtId"
                            @change="onDistrictChanged()"
                            class="input-ds min-h-[52px] w-full rounded-xl border-neutral-200 bg-neutral-50/50 text-[15px]">
                            <option value="" disabled>{{ __('Önce ili seç') }}</option>
                            <template x-for="dist in districts" :key="dist.id">
                                <option :value="String(dist.id)" x-text="dist.name"></option>
                            </template>
                        </select>
                        <p class="mt-2 text-[12px] text-neutral-500" x-show="cityId && districts.length===0">{{ __('Bu il için ilçe bulunamadı — geo senkronu gerekli.') }}</p>
                        @error('district_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('Mahalle') }}
                            <span class="text-red-600">*</span></label>
                        <input type="hidden" name="neighborhood_name" id="quick-neighborhood-name" :value="neighborhoodName">
                        <select id="quick-neighborhood" name="neighborhood_turkiye_id" required x-model="neighborhoodTurkiyeId"
                            @change="onNeighborhoodPick($event)"
                            class="input-ds min-h-[52px] w-full rounded-xl border-neutral-200 bg-neutral-50/50 text-[15px]">
                            <option value="">{{ __('İlçe seçildikten sonra yüklenir') }}</option>
                            <template x-for="n in neighborhoods" :key="n.id">
                                <option :value="String(n.id)" x-text="n.name"></option>
                            </template>
                        </select>
                        @error('neighborhood_turkiye_id')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        @error('neighborhood_name')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-teal-100 bg-teal-50/30 p-4 ring-1 ring-teal-100/80">
                        <p class="text-xs font-bold uppercase tracking-wide text-teal-900">{{ __('Konum (harita)') }}</p>
                        <p class="mt-1 text-[13px] font-medium text-teal-950/90">{{ __('Cihazınızın konumunu alın; haritada işaretlenen nokta kayda geçer (isteğe bağlı).') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button"
                                class="inline-flex min-h-[44px] items-center justify-center rounded-full border border-teal-600 bg-teal-600 px-5 text-[14px] font-bold text-white shadow-sm transition hover:bg-teal-700"
                                @click="pullLocation()">{{ __('Konum çek') }}</button>
                            <button type="button"
                                class="inline-flex min-h-[44px] items-center justify-center rounded-full border border-teal-200 bg-white px-5 text-[14px] font-bold text-teal-900 hover:bg-teal-50"
                                x-show="latitude !== '' && longitude !== ''"
                                @click="clearLocation()">{{ __('Konumu temizle') }}</button>
                        </div>
                        @error('latitude')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        @error('longitude')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="relative mt-4 overflow-hidden rounded-xl border border-teal-100 bg-neutral-200/40"
                            x-show="hasMapCoords()" style="aspect-ratio: 16/10;">
                            <iframe title="{{ __('Seçilen konum — OpenStreetMap') }}" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="absolute inset-0 h-full w-full border-0"
                                x-bind:src="mapEmbedSrc()"></iframe>
                        </div>
                        <p class="mt-2 text-[12px] font-semibold text-teal-900" x-show="latitude !== '' && longitude !== ''">
                            <span>{{ __('Koordinat:') }}</span>
                            <span x-text="latitude"></span>,
                            <span x-text="longitude"></span>
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-between gap-2 pt-1">
                        <button type="button"
                            class="inline-flex min-h-[50px] items-center justify-center rounded-full border border-neutral-200 bg-white px-6 text-[15px] font-bold text-neutral-800 hover:bg-neutral-50"
                            @click="prevStep()">{{ __('Geri') }}</button>
                        <button type="button" class="btn-primary inline-flex min-h-[50px] items-center justify-center rounded-full px-8 text-[15px] font-bold shadow-cta"
                            @click="nextStep()">{{ __('İleri: özet') }}</button>
                    </div>
                </div>

                {{-- Adım 3: Başlık (kişisel özet), doğrulama, gönder --}}
                <div x-show="wizardStep === 3" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 3">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-neutral-500" for="quick-title">{{ __('Kısa başlık') }}
                            <span class="text-red-600">*</span></label>
                        <input id="quick-title" name="title" value="{{ old('title', $d['title'] ?? '') }}" required maxlength="255"
                            class="input-ds min-h-[52px] w-full rounded-xl border-neutral-200 text-[15px]"
                            placeholder="{{ __('Tek cümlelik özet (listelerde görünür)') }}">
                        @error('title')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @guest
                        <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-950 ring-1 ring-amber-100">
                            {{ __('Üye değilsen gönderimden sonra kayıt ve telefon doğrulamasına yönlendirilirsin.') }}</p>
                    @endguest

                    <x-turnstile-widget class="rounded-xl border border-neutral-100 bg-neutral-50 px-3 py-4" />

                    <div class="flex flex-wrap justify-between gap-2">
                        <button type="button"
                            class="inline-flex min-h-[50px] items-center justify-center rounded-full border border-neutral-200 bg-white px-6 text-[15px] font-bold text-neutral-800 hover:bg-neutral-50"
                            @click="prevStep()">{{ __('Geri') }}</button>
                        <button type="submit"
                            class="btn-primary flex min-h-[54px] flex-1 items-center justify-center rounded-full text-[16px] font-bold shadow-cta transition active:scale-[0.99] sm:flex-initial sm:px-12">
                            {{ __('Gönder') }}</button>
                    </div>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.__complaintMaxImages = {{ (int) config('complaint.max_images', 5) }};
        window.__complaintMaxVideos = {{ (int) config('complaint.max_videos', 2) }};
        window.__complaintImageMaxKb = {{ (int) config('complaint.image_max_kb', 6144) }};
        window.__complaintVideoMaxKb = {{ (int) config('complaint.video_max_kb', 35840) }};
    </script>
@endpush
