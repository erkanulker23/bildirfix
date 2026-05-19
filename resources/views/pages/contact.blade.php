@extends('layouts.app')

@section('title', __('İletişim').' • '.config('app.name'))

@php
    $primaryChannel = collect(config('contact.channels', []))->first();
@endphp

@section('content')
    <div class="mx-auto max-w-[720px] px-4 pb-16 pt-2 sm:px-5">
        @if ($primaryChannel)
            <p class="mb-8 text-sm text-neutral-700">
                <span class="font-bold text-neutral-900">{{ __(data_get($primaryChannel, 'label')) }}</span>
                <span class="mx-1.5 text-neutral-400" aria-hidden="true">·</span>
                <a href="mailto:{{ data_get($primaryChannel, 'value') }}" class="font-semibold text-primary underline-offset-4 hover:underline">{{ data_get($primaryChannel, 'value') }}</a>
            </p>
        @endif

        <article class="rounded-3xl border border-teal-100/80 bg-white p-6 shadow-xl shadow-teal-500/[0.07] ring-1 ring-teal-50 sm:p-8">
            <h2 class="text-lg font-black tracking-tight text-teal-950">{{ __('Mesaj bırakın') }}</h2>
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
@endsection
