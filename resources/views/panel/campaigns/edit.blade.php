@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Kampanya düzenle'))

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kampanya düzenle') }}</h1>
                <p class="psc-page-desc">{{ \Illuminate\Support\Str::limit($campaign->title, 60) }}</p>
            </div>
            <a href="{{ route('panel.campaigns.index') }}" class="psc-btn psc-btn--ghost">← {{ __('Listeye dön') }}</a>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @include('partials.panel-moderation-badge', ['status' => $campaign->moderation_status])
            <span class="text-xs text-[#64748b]">{{ number_format((int) $campaign->supporter_count) }} {{ __('destek') }}</span>
            <span class="text-xs text-[#64748b]">· {{ number_format((int) $campaign->view_count) }} {{ __('görüntülenme') }}</span>
            @if ($campaign->isPubliclyApproved())
                <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-[#ea580c] hover:underline">{{ __('Yayında gör') }}</a>
            @endif
        </div>

        @if (! $canEdit)
            <div class="psc-alert psc-alert--warn">{{ __('Yayındaki kampanyalar düzenlenemez. Yalnızca görüntüleyebilirsin.') }}</div>
        @endif

        @if ($campaign->moderation_note)
            <div class="psc-alert psc-alert--error">
                <p class="font-semibold">{{ __('Moderasyon notu') }}</p>
                <p class="mt-1 text-sm">{{ $campaign->moderation_note }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('panel.campaigns.update', $campaign) }}" class="psc-card">
            <div class="psc-card__body space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="psc-field__label" for="cmp-title">{{ __('Başlık') }}</label>
                    <input id="cmp-title" name="title" type="text" required maxlength="140"
                        value="{{ old('title', $campaign->title) }}" class="psc-input mt-2" @disabled(! $canEdit)>
                    @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label" for="cmp-excerpt">{{ __('Özet') }}</label>
                    <textarea id="cmp-excerpt" name="excerpt" rows="2" maxlength="480" class="psc-input mt-2" @disabled(! $canEdit)>{{ old('excerpt', $campaign->excerpt) }}</textarea>
                    @error('excerpt')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label" for="cmp-description">{{ __('Açıklama') }}</label>
                    <textarea id="cmp-description" name="description" rows="10" required maxlength="20000"
                        class="psc-input mt-2 min-h-[12rem]" @disabled(! $canEdit)>{{ old('description', $campaign->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label" for="cmp-hero">{{ __('Kapak görseli URL') }}</label>
                    <input id="cmp-hero" name="hero_image_url" type="url" maxlength="2000"
                        value="{{ old('hero_image_url', $campaign->hero_image_url) }}" class="psc-input mt-2" @disabled(! $canEdit)>
                    @error('hero_image_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="psc-field__label" for="cmp-city">{{ __('İl') }}</label>
                        <select id="cmp-city" name="city_id" class="psc-select mt-2" @disabled(! $canEdit)>
                            <option value="">{{ __('Genel (Türkiye)') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" @selected((int) old('city_id', $campaign->city_id) === (int) $city->id)>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="psc-field__label" for="cmp-topic">{{ __('Konu') }}</label>
                        <select id="cmp-topic" name="campaign_topic_id" class="psc-select mt-2" @disabled(! $canEdit)>
                            <option value="">{{ __('Seçilmedi') }}</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}" @selected((int) old('campaign_topic_id', $campaign->campaign_topic_id) === (int) $topic->id)>{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('campaign_topic_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="psc-field__label" for="cmp-goal">{{ __('Hedef destekçi sayısı') }}</label>
                    <input id="cmp-goal" name="goal_supporters" type="number" min="10"
                        value="{{ old('goal_supporters', $campaign->goal_supporters) }}" class="psc-input mt-2" @disabled(! $canEdit)>
                    @error('goal_supporters')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                @if ($canEdit)
                    <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
                @endif
            </div>
        </form>
    </div>
@endsection
