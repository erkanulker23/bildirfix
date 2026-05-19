@php
    $d = $complaintDraft ?? [];
    $wizardStep = 0;
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

<div class="complaint-wizard mx-auto w-full min-w-0 max-w-xl px-3 pb-28 pt-6 sm:px-0"
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
        suggestedInstitutions: @js(($suggestedInstitutions ?? collect())->map(fn ($i) => ['id' => (int) $i->id, 'name' => $i->name])->values()->all()),
        urls: {
            districts: @js(route('geo.districts')),
            neighborhoods: @js(route('geo.neighborhoods')),
            institutions: @js(route('geo.institutions')),
            reverse: @js(route('geo.reverse')),
        },
    })"
    x-init="initQuick()"
    x-cloak>

    <div class="mb-8 flex justify-center gap-2" x-show="wizardStep >= 1 && wizardStep <= 3">
        <template x-for="i in 3" :key="i">
            <span class="h-1.5 w-10 rounded-full transition"
                :class="wizardStep >= i ? 'bg-primary' : 'bg-neutral-200'"></span>
        </template>
    </div>

    @php
        $geoHint = \App\Models\District::query()->whereNotNull('turkiye_id')->doesntExist();
    @endphp
    @if ($geoHint)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[13px] font-semibold text-amber-950">
            {{ __('Tam il/ilçe listesi için:') }}
            <code class="rounded bg-white/70 px-1.5">php artisan turkiye:sync-geo</code>
        </div>
    @endif

    <form id="quick-complaint-form" method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data"
        @submit="guardQuickComplaint($event)">
        @csrf
        <input type="hidden" name="category_id" x-bind:value="categoryId">
        <input type="hidden" name="latitude" x-bind:value="latitude !== '' && latitude !== null ? latitude : ''">
        <input type="hidden" name="longitude" x-bind:value="longitude !== '' && longitude !== null ? longitude : ''">
        <template x-for="row in selectedInstitutions" :key="row.id">
            <input type="hidden" name="institution_ids[]" :value="row.id">
        </template>

        <div x-show="wizardStep === 0" class="text-center">
            <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-orange-50 ring-1 ring-orange-100" aria-hidden="true">
                <svg class="h-12 w-12 text-primary" fill="none" viewBox="0 0 48 48" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M24 8v8m0 16v8M8 24h8m16 0h8M14 14l5.5 5.5M28.5 28.5 34 34M34 14l-5.5 5.5M18.5 28.5 14 34" />
                    <circle cx="24" cy="24" r="6" />
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">
                {{ __('Kent sorununu birlikte görünür kılalım') }}
            </h1>
            <p class="mt-4 text-[15px] font-medium leading-relaxed text-neutral-600">
                {{ __('Üç kısa adımda bildiriminizi oluşturun: ne oldu, nerede, kısa başlık. Fotoğraf veya video ekleyebilirsiniz.') }}
            </p>
            <button type="button" @click="startComplaintWizard()"
                class="mt-10 rounded-full bg-amber-400 px-10 py-3.5 text-sm font-black text-neutral-900 shadow-md hover:bg-amber-300">
                {{ __('Devam et') }}
            </button>
        </div>

        {{-- Adım 1 --}}
        <div x-show="wizardStep === 1" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 1">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">
                {{ __('Ne oldu? Sorunu anlatın') }}
            </h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">
                {{ __('İsterseniz kategori seçin; fotoğraf, video veya sesli anlatım ekleyebilirsiniz.') }}
            </p>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-neutral-900"
                    for="quick-description">{{ __('Ne oldu?') }} <span class="text-red-600">*</span></label>
                <div class="relative">
                    <textarea id="quick-description" name="description" rows="7" maxlength="8000" required
                        class="input-ds min-h-[10rem] w-full resize-y rounded-xl border-neutral-200 pr-14 text-[15px] leading-relaxed"
                        placeholder="{{ __('Ne yaşandı, nerede, ne zaman; beklentiniz…') }}">{{ old('description', $d['description'] ?? '') }}</textarea>
                    <button type="button"
                        class="absolute bottom-3 right-3 flex h-10 w-10 items-center justify-center rounded-full text-white shadow-md transition disabled:cursor-not-allowed disabled:opacity-40"
                        :class="speechListening
                            ? 'bg-red-500 ring-2 ring-red-300/60 animate-pulse'
                            : 'bg-primary hover:bg-primary-hover'"
                        :disabled="!speechSupported"
                        @click="toggleSpeechInput()"
                        :title="speechSupported ? (speechListening ? '{{ __('Dinlemeyi durdur') }}' : '{{ __('Sesle yaz') }}') : '{{ __('Sesli yazma bu tarayıcıda desteklenmiyor') }}'"
                        aria-label="{{ __('Mikrofon ile sesli yaz') }}">
                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 14a3 3 0 0 0 3-3V6a3 3 0 1 0-6 0v5a3 3 0 0 0 3 3Zm-7-3a7 7 0 0 0 14 0h2a9 9 0 0 1-8 8.94V22h-2v-2.06A9 9 0 0 1 3 11h2Z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-1.5 text-[12px] font-medium text-primary" x-show="speechListening" x-cloak>{{ __('Dinleniyor… Konuştuğunuz metin anında yazılır.') }}</p>
                    @error('description')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('Kategori') }}
                        <span class="font-normal normal-case text-neutral-400">({{ __('isteğe bağlı') }})</span></p>
                    <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                        @foreach ($categories as $cat)
                            <button type="button"
                                class="inline-flex min-h-[46px] min-w-0 w-full items-center gap-2 rounded-full border-2 px-3 py-2 text-left text-[13px] font-bold leading-snug transition sm:w-auto sm:px-4 sm:text-sm"
                                @click="categoryId = '{{ $cat->id }}'"
                                :class="categoryId === '{{ $cat->id }}'
                                    ? 'border-primary bg-primary-light text-primary shadow-sm ring-1 ring-primary/25'
                                    : 'border-neutral-100 bg-neutral-50 text-neutral-800 hover:border-neutral-200'">
                                <span
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-sm font-black text-primary ring-1 ring-primary/15">{{ mb_substr($cat->name, 0, 1) }}</span>
                                <span class="min-w-0 flex-1 break-words">{{ $cat->name }}</span>
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
                    <div class="mb-3 flex flex-wrap gap-2" x-show="suggestedInstitutions.length > 0 && institutionSearch.length < 2">
                        <template x-for="row in suggestedInstitutions" :key="'sug-' + row.id">
                            <button type="button"
                                class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 text-[12px] font-bold text-neutral-800 hover:border-primary/35 hover:bg-primary-light/50"
                                x-text="row.name"
                                @click="addInstitution(row)"></button>
                        </template>
                    </div>
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

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="prevStep()"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="nextStep()"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 2 --}}
        <div x-show="wizardStep === 2" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 2">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">{{ __('Konum nerede?') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('İl, ilçe, mahalle ve isteğe bağlı harita konumu.') }}</p>
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
                            @click="pullLocation()"
                            :disabled="locationResolving">
                            <span x-show="!locationResolving">{{ __('Konum çek') }}</span>
                            <span x-show="locationResolving" x-cloak>{{ __('Adres bulunuyor…') }}</span>
                        </button>
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

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="prevStep()"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="nextStep()"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 3 --}}
        <div x-show="wizardStep === 3" x-cloak class="space-y-5" x-bind:inert="wizardStep !== 3">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">{{ __('Son adım: kısa başlık') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('Listede görünecek tek cümlelik özet.') }}</p>
            <div>
                <label class="mb-1.5 block text-sm font-bold text-neutral-900" for="quick-title">{{ __('Kısa başlık') }}
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

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="prevStep()"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="submit"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Gönder') }}</button>
            </div>
        </div>
    </form>
</div>
