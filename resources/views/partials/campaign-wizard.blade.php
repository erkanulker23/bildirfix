@php
    $formAction = $formAction ?? route('campaigns.store');
    $cancelUrl = $cancelUrl ?? route('campaigns.index');
    $submitLabel = $submitLabel ?? __('Kampanyayı gönder');
    $campaignTopics = $campaignTopics ?? collect();
    $topicsJson = $campaignTopics->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'group' => $t->group_key])->values();
@endphp

<div class="mx-auto max-w-xl" x-data="campaignWizard()" x-cloak>
    <div class="mb-8 flex justify-center gap-2" x-show="step > 0 && step < 7">
        <template x-for="i in 6" :key="i">
            <span class="h-1.5 w-8 rounded-full transition"
                :class="step >= i ? 'bg-primary' : 'bg-neutral-200'"></span>
        </template>
    </div>

    <form method="POST" action="{{ $formAction }}" @submit="onSubmit">
        @csrf

        {{-- Adım 0: Karşılama --}}
        <div x-show="step === 0" class="text-center">
            <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-emerald-50 ring-1 ring-emerald-100" aria-hidden="true">
                <svg class="h-12 w-12 text-emerald-600" fill="none" viewBox="0 0 48 48" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M24 8v8m0 16v8M8 24h8m16 0h8M14 14l5.5 5.5M28.5 28.5 34 34M34 14l-5.5 5.5M18.5 28.5 14 34" />
                    <circle cx="24" cy="24" r="6" />
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">
                {{ __('Güçlü bir kampanya oluşturmanda sana yardımcı olacağız') }}
            </h1>
            <p class="mt-4 text-[15px] font-medium leading-relaxed text-neutral-600">
                {{ __('Sadece birkaç cümle yaz; amacını, hikâyeni ve kapsamını adım adım toplayalım. Şehir seçimi yalnızca Türkiye illeriyle sınırlıdır.') }}
            </p>
            <button type="button" @click="step = 1"
                class="mt-10 rounded-full bg-amber-400 px-10 py-3.5 text-sm font-black text-neutral-900 shadow-md hover:bg-amber-300">
                {{ __('Devam et') }}
            </button>
        </div>

        {{-- Adım 1: Amaç --}}
        <div x-show="step === 1">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950 sm:text-3xl">
                {{ __('Öncelikle sorunun ne olduğundan bahset') }}
            </h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">
                {{ __('Senin sözlerini platformumuzla birleştirerek etkili bir kampanya taslağı oluşturacağız.') }}
            </p>
            <div class="mt-8">
                <label class="text-sm font-bold text-neutral-900">{{ __('İsterim ki…') }}</label>
                <textarea name="purpose" x-model="purpose" rows="5" required maxlength="2000"
                    placeholder="{{ __('Örn: SMA hastalarının ilaç masrafları için kamu desteği sağlansın.') }}"
                    class="mt-2 w-full rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-[15px] font-medium text-neutral-900 shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"></textarea>
                @error('purpose')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = 0"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="nextFromPurpose()"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 2: Kişisel hikaye --}}
        <div x-show="step === 2">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950">{{ __('Son bir şey daha') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">
                {{ __('Kişisel bir hikaye eklemek kampanyayı daha güçlü hale getirir.') }}
            </p>
            <div class="mt-8">
                <label class="text-sm font-bold text-neutral-900">{{ __('Bu senin için neden kişisel? (isteğe bağlı)') }}</label>
                <textarea name="personal_story" x-model="personalStory" rows="5" maxlength="5000"
                    placeholder="{{ __('Örn: Yeğenim SMA tip 2 tanısıyla mücadele ediyor; ailemiz tedaviyi karşılayamıyor.') }}"
                    class="mt-2 w-full rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-[15px] font-medium text-neutral-900 shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"></textarea>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = 1"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="step = 3"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 3: Konu --}}
        <div x-show="step === 3">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950">{{ __('Kampanyanın konusu nedir?') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('Bir konu etiketi seç (ör. Sağlık, Hasta Hakları).') }}</p>
            <input type="hidden" name="campaign_topic_id" :value="topicId">
            <div class="mt-6">
                <input type="search" x-model="topicQuery" placeholder="{{ __('Konu ara…') }}"
                    class="w-full rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mt-4 max-h-[min(50vh,22rem)] overflow-y-auto rounded-2xl border border-neutral-100 bg-neutral-50/50 p-3">
                <div class="flex flex-wrap gap-2">
                    <template x-for="t in filteredTopics" :key="t.id">
                        <button type="button" @click="topicId = t.id"
                            class="rounded-full border px-3 py-1.5 text-xs font-bold transition"
                            :class="Number(topicId) === Number(t.id) ? 'border-sky-600 bg-sky-50 text-sky-900' : 'border-sky-200 bg-white text-sky-950 hover:bg-sky-50'"
                            x-text="t.name"></button>
                    </template>
                </div>
            </div>
            <p x-show="selectedTopicName" class="mt-3 text-center text-sm font-bold text-emerald-800" x-text="'✓ ' + selectedTopicName"></p>
            @error('campaign_topic_id')<p class="mt-2 text-center text-sm text-rose-600">{{ $message }}</p>@enderror
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = 2" class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold">{{ __('Geri') }}</button>
                <button type="button" @click="nextFromTopic()" class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 4: Kapsam --}}
        <div x-show="step === 4">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950">{{ __('Kampanyanın kapsamı nedir?') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('Türkiye genelinde mi, yoksa belirli bir ilde mi odaklanacaksın?') }}</p>
            <input type="hidden" name="scope" :value="scope">
            <div class="mt-8 grid grid-cols-3 gap-3 sm:gap-4">
                <button type="button" @click="selectScope('local')"
                    class="flex flex-col items-center rounded-2xl border-2 bg-white p-4 shadow-sm transition hover:shadow-md"
                    :class="scope === 'local' ? 'border-amber-400 ring-2 ring-amber-200' : 'border-neutral-200'">
                    <svg class="mb-2 h-10 w-10 text-neutral-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10" />
                    </svg>
                    <span class="text-sm font-black text-neutral-900">{{ __('Yerel') }}</span>
                    <span class="mt-1 text-[10px] font-medium text-neutral-500">{{ __('Bir il') }}</span>
                </button>
                <button type="button" @click="selectScope('national')"
                    class="flex flex-col items-center rounded-2xl border-2 bg-white p-4 shadow-sm transition hover:shadow-md"
                    :class="scope === 'national' ? 'border-amber-400 ring-2 ring-amber-200' : 'border-neutral-200'">
                    <svg class="mb-2 h-10 w-10 text-neutral-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 21h8M12 17v4M6 4h12v7a6 6 0 01-12 0V4z" />
                    </svg>
                    <span class="text-sm font-black text-neutral-900">{{ __('Ulusal') }}</span>
                    <span class="mt-1 text-[10px] font-medium text-neutral-500">{{ __('Tüm Türkiye') }}</span>
                </button>
                <button type="button" @click="selectScope('global')"
                    class="flex flex-col items-center rounded-2xl border-2 bg-white p-4 shadow-sm transition hover:shadow-md"
                    :class="scope === 'global' ? 'border-amber-400 ring-2 ring-amber-200' : 'border-neutral-200'">
                    <svg class="mb-2 h-10 w-10 text-neutral-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M12 3c2.5 2.5 4 5.5 4 9s-1.5 6.5-4 9c-2.5-2.5-4-5.5-4-9s1.5-6.5 4-9z" />
                    </svg>
                    <span class="text-sm font-black text-neutral-900">{{ __('Geniş') }}</span>
                    <span class="mt-1 text-[10px] font-medium text-neutral-500">{{ __('Ülke çapında') }}</span>
                </button>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = 3"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="nextFromScope()"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 5: İl seçimi (yerel) --}}
        <div x-show="step === 5">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950">{{ __('Hangi il?') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('Yalnızca Türkiye illeri listelenir.') }}</p>
            <input type="hidden" name="city_id" :value="cityId">
            <div class="relative mt-8">
                <label class="text-sm font-bold text-neutral-900">{{ __('İl ara') }} <span class="text-rose-600">*</span></label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" aria-hidden="true">⌕</span>
                    <input type="text" x-model="cityQuery" @input.debounce.250ms="searchCities()" @focus="searchCities()"
                        autocomplete="off" placeholder="{{ __('Örn: İstanbul') }}"
                        class="w-full rounded-2xl border border-neutral-200 bg-white py-3 pl-10 pr-4 text-[15px] font-semibold shadow-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <ul x-show="cityResults.length > 0 && cityQuery.length >= 2"
                    class="absolute z-20 mt-1 max-h-56 w-full overflow-auto rounded-2xl border border-neutral-200 bg-white py-1 shadow-lg">
                    <template x-for="c in cityResults" :key="c.id">
                        <li>
                            <button type="button" @click="pickCity(c)"
                                class="w-full px-4 py-2.5 text-left text-sm font-semibold text-neutral-800 hover:bg-primary-light"
                                x-text="c.label + ', Türkiye'"></button>
                        </li>
                    </template>
                </ul>
                <p x-show="cityLabel" class="mt-3 text-sm font-bold text-emerald-800" x-text="'✓ ' + cityLabel + ', Türkiye'"></p>
                @error('city_id')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = 4"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="button" @click="nextFromCity()"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ __('Devam et') }}</button>
            </div>
        </div>

        {{-- Adım 6: Önizleme --}}
        <div x-show="step === 6">
            <h2 class="text-center text-2xl font-black tracking-tight text-neutral-950">{{ __('Kampanyan kayda hazır') }}</h2>
            <p class="mt-3 text-center text-sm font-medium text-neutral-600">{{ __('Başlığı düzenleyebilir, isteğe bağlı hedef ve kapak ekleyebilirsin.') }}</p>

            <div class="mt-8 space-y-4 rounded-2xl border border-neutral-200 bg-neutral-50/80 p-5">
                <div>
                    <label class="text-sm font-bold text-neutral-900">{{ __('Kampanya başlığı') }}</label>
                    <input name="title" x-model="title" required maxlength="140"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold">
                    @error('title')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-bold text-neutral-900">{{ __('Destekçi hedefi (isteğe bağlı, ≥10)') }}</label>
                    <input name="goal_supporters" type="number" min="10" x-model="goalSupporters"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="text-sm font-bold text-neutral-900">{{ __('Kapak görseli URL (isteğe bağlı)') }}</label>
                    <input name="hero_image_url" type="url" x-model="heroImageUrl" placeholder="https://"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm">
                </div>
                <div class="rounded-xl bg-sky-50 px-4 py-3 text-xs font-medium text-sky-900 ring-1 ring-sky-100">
                    {{ __('Gönderdikten sonra süper yönetici onayı gerekir. Onaylanınca kampanya listelerde görünür.') }}
                </div>
            </div>

            <input type="hidden" name="_wizard_step" value="6">
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="step = scope === 'local' ? 5 : 4"
                    class="rounded-full border border-neutral-300 bg-white px-6 py-2.5 text-sm font-bold text-neutral-900 hover:bg-neutral-50">{{ __('Geri') }}</button>
                <button type="submit"
                    class="rounded-full bg-amber-400 px-8 py-2.5 text-sm font-black text-neutral-900 hover:bg-amber-300">{{ $submitLabel }}</button>
            </div>
        </div>
    </form>

    <p class="mt-8 text-center">
        <a href="{{ $cancelUrl }}" class="text-xs font-bold text-neutral-500 hover:text-neutral-800">{{ __('Vazgeç') }}</a>
    </p>
</div>

@push('scripts')
    <script>
        function campaignWizard() {
            return {
                step: {{ (int) old('_wizard_step', old('purpose') ? 6 : 0) }},
                purpose: @js(old('purpose', '')),
                personalStory: @js(old('personal_story', '')),
                topics: @js($topicsJson),
                topicId: @js(old('campaign_topic_id', '')),
                topicQuery: '',
                scope: @js(old('scope', 'national')),
                cityId: @js(old('city_id', '')),
                cityQuery: '',
                cityLabel: '',
                cityResults: [],
                title: @js(old('title', '')),
                goalSupporters: @js(old('goal_supporters', '')),
                heroImageUrl: @js(old('hero_image_url', '')),
                searchUrl: @js(route('api.cities.search')),

                get filteredTopics() {
                    const q = (this.topicQuery || '').trim().toLocaleLowerCase('tr-TR');
                    if (!q) return this.topics;
                    return this.topics.filter((t) => (t.name || '').toLocaleLowerCase('tr-TR').includes(q));
                },

                get selectedTopicName() {
                    const t = this.topics.find((x) => Number(x.id) === Number(this.topicId));
                    return t ? t.name : '';
                },

                nextFromTopic() {
                    if (!this.topicId) {
                        alert(@js(__('Lütfen bir konu seçin.')));
                        return;
                    }
                    this.step = 4;
                },

                nextFromPurpose() {
                    if ((this.purpose || '').trim().length < 10) {
                        alert(@js(__('Lütfen kampanyanın amacını birkaç cümleyle anlatın (en az 10 karakter).')));
                        return;
                    }
                    if (!this.title) {
                        this.title = this.buildTitle(this.purpose);
                    }
                    this.step = 2;
                },

                selectScope(s) {
                    this.scope = s;
                    if (s !== 'local') {
                        this.cityId = '';
                        this.cityLabel = '';
                    }
                },

                nextFromScope() {
                    if (!this.scope) return;
                    if (this.scope === 'local') {
                        this.step = 5;
                    } else {
                        this.cityId = '';
                        this.step = 6;
                    }
                },

                async searchCities() {
                    const q = (this.cityQuery || '').trim();
                    if (q.length < 2) {
                        this.cityResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(q), {
                            headers: { 'Accept': 'application/json' },
                        });
                        this.cityResults = await res.json();
                    } catch {
                        this.cityResults = [];
                    }
                },

                pickCity(c) {
                    this.cityId = c.id;
                    this.cityLabel = c.label;
                    this.cityQuery = c.name;
                    this.cityResults = [];
                },

                nextFromCity() {
                    if (!this.cityId) {
                        alert(@js(__('Lütfen bir il seçin.')));
                        return;
                    }
                    this.step = 6;
                },

                buildTitle(purpose) {
                    let t = (purpose || '').trim();
                    const lower = t.toLocaleLowerCase('tr-TR');
                    if (lower.startsWith('isterim ki ')) t = t.slice(11).trim();
                    if (lower.startsWith('istiyorum ki ')) t = t.slice(13).trim();
                    return t.length > 140 ? t.slice(0, 137) + '…' : t;
                },

                onSubmit(e) {
                    if (this.step !== 6) {
                        e.preventDefault();
                    }
                },
            };
        }
    </script>
@endpush
