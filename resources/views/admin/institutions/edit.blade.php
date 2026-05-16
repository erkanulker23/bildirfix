@extends('layouts.admin')

@section('admin_heading', $institution->name)
@section('title', __('Kurum düzenle'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Kurum bilgileri') }}</h1>
            <a href="{{ route('admin.institutions.index') }}" class="text-xs font-bold text-blue-600 hover:underline">← {{ __('Listeye dön') }}</a>
        </div>

        <form method="POST" action="{{ route('admin.institutions.update', $institution) }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Ad') }}</label>
                <input name="name" type="text" required value="{{ old('name', $institution->name) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                @error('name')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Tür') }}</label>
                    <input name="type" type="text" value="{{ old('type', $institution->type) }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Şehir') }}</label>
                    <select name="city_id"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="">{{ __('Seçin') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected((string) old('city_id', $institution->city_id) === (string) $city->id)>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Doğrulanmış kurum') }}</label>
                <select name="verified"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="1" @selected(old('verified', $institution->verified ? '1' : '0') === '1')>{{ __('Evet') }}</option>
                    <option value="0" @selected(old('verified', $institution->verified ? '1' : '0') === '0')>{{ __('Hayır') }}</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Web sitesi') }}</label>
                <input name="website" type="url" value="{{ old('website', $institution->website) }}" placeholder="https://"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Kamu e-posta') }}</label>
                <input name="public_email" type="email" value="{{ old('public_email', $institution->public_email) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Telefon') }}</label>
                <input name="phone" type="text" value="{{ old('phone', $institution->phone) }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Adres') }}</label>
                <textarea name="address" rows="2"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('address', $institution->address) }}</textarea>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-blue-600 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kaydet') }}</button>
        </form>
    </div>
@endsection
