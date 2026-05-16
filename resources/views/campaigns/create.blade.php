@extends('layouts.app')

@section('toolbar')
    <div></div>
@endsection

@section('title', __('Kampanya başlat'))

@section('content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-indigo-100 bg-white p-6 shadow-lg ring-1 ring-indigo-50 sm:p-8">
        <p class="text-[11px] font-black uppercase tracking-[0.2em] text-indigo-700">{{ __('Sosyal sorumluluk') }}</p>
        <h1 class="mt-2 text-2xl font-black tracking-tight text-neutral-950">{{ __('Topluma açık kampanya taslağı') }}</h1>
        <p class="mt-3 text-sm font-semibold leading-relaxed text-neutral-700">{{ __('Gönderdikten sonra süper yönetici yayına uygun görürse kampanyanız liste ve ana sayfadaki CSR alanında yer alır. Destekçiler doğrulanmış kullanıcılarla toplanır.') }}</p>

        <form method="POST" action="{{ route('campaigns.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="ctp-title" class="text-[13px] font-bold text-neutral-800">{{ __('Kampanya başlığı') }}</label>
                <input id="ctp-title" name="title" value="{{ old('title') }}" required maxlength="140"
                    class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">
                @error('title')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ctp-excerpt" class="text-[13px] font-bold text-neutral-800">{{ __('Kısa özet') }}
                    <span class="font-normal text-neutral-500">{{ __('kartlarda kullanılır') }}</span></label>
                <textarea id="ctp-excerpt" name="excerpt" rows="3" maxlength="480"
                    class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ctp-body" class="text-[13px] font-bold text-neutral-800">{{ __('Açıklama ve çağrı') }}</label>
                <textarea id="ctp-body" name="description" rows="10" required
                    class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ctp-cover" class="text-[13px] font-bold text-neutral-800">{{ __('Kapak görseli URL (isteğe bağlı)') }}</label>
                <input id="ctp-cover" name="hero_image_url" value="{{ old('hero_image_url') }}" type="url"
                    placeholder="https://..."
                    class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">
                @error('hero_image_url')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="ctp-city" class="text-[13px] font-bold text-neutral-800">{{ __('Şehir odağı (boşsa genel)') }}</label>
                    <select id="ctp-city" name="city_id"
                        class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Genel Türkiye') }}</option>
                        @foreach ($cities as $c)
                            <option value="{{ $c->id }}" @selected((string) old('city_id') === (string) $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ctp-goal" class="text-[13px] font-bold text-neutral-800">{{ __('Destekçi hedefi (isteğe bağlı, ≥10)') }}</label>
                    <input id="ctp-goal" name="goal_supporters" value="{{ old('goal_supporters') }}" inputmode="numeric"
                        placeholder="ör. 250"
                        class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">
                    @error('goal_supporters')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="ctp-ends" class="text-[13px] font-bold text-neutral-800">{{ __('Kampanya son tarihi (isteğe bağlı)') }}</label>
                <input id="ctp-ends" name="ends_at" value="{{ old('ends_at') }}" type="datetime-local"
                    class="mt-2 w-full rounded-xl border-neutral-200 bg-neutral-50 px-4 py-3 text-neutral-950 shadow-inner focus:border-indigo-500 focus:ring-indigo-500">
                @error('ends_at')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit"
                    class="rounded-full bg-indigo-600 px-8 py-3.5 text-[13px] font-black uppercase tracking-wider text-white shadow-lg hover:bg-indigo-700">{{ __('Moderasyona gönder') }}</button>
                <a href="{{ route('campaigns.index') }}"
                    class="rounded-full bg-neutral-200 px-6 py-3.5 text-[13px] font-bold text-neutral-900 hover:bg-neutral-300">{{ __('Vazgeç') }}</a>
            </div>
        </form>
    </section>
@endsection
