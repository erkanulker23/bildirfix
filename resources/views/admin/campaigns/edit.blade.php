@extends('layouts.admin')

@section('admin_heading', $campaign->title)
@section('title', __('Kampanya düzenle'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kampanya düzenle') }}</h1>
            <a href="{{ route('admin.campaigns.registry', request()->only('durum', 'q', 'page')) }}"
                class="text-xs font-bold text-blue-600 hover:underline">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Başlık') }}</label>
                <input name="title" type="text" required value="{{ old('title', $campaign->title) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Özet') }}</label>
                <input name="excerpt" type="text" value="{{ old('excerpt', $campaign->excerpt) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Açıklama') }}</label>
                <textarea name="description" rows="6"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ old('description', $campaign->description) }}</textarea>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Konu') }}</label>
                <select name="campaign_topic_id" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">{{ __('Seçin') }}</option>
                    @foreach ($topics as $topic)
                        <option value="{{ $topic->id }}" @selected((string) old('campaign_topic_id', $campaign->campaign_topic_id) === (string) $topic->id)>{{ $topic->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Şehir') }}</label>
                    <select name="city_id" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="">{{ __('Seçin') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected((string) old('city_id', $campaign->city_id) === (string) $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Hedef destekçi') }}</label>
                    <input name="goal_supporters" type="number" min="0" value="{{ old('goal_supporters', $campaign->goal_supporters) }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Kapak görseli URL') }}</label>
                <input name="hero_image_url" type="text" value="{{ old('hero_image_url', $campaign->hero_image_url) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Moderasyon durumu') }}</label>
                <select name="moderation_status" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('moderation_status', $campaign->moderation_status->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Moderasyon notu') }}</label>
                <textarea name="moderation_note" rows="2"
                    class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">{{ old('moderation_note', $campaign->moderation_note) }}</textarea>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
