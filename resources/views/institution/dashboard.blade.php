@extends('layouts.app')

@section('title', __('Kurum paneli'))

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Kurum hesabı') }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ $institution ? $institution->name : __('Kurum profilin henüz atanmadı.') }}
        </p>
        @if ($institution)
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">{{ __('Şehir') }}</dt>
                    <dd>{{ $institution->city?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">{{ __('Durum') }}</dt>
                    <dd>{{ $institution->verified ? __('Doğrulanmış') : __('Taslak') }}</dd>
                </div>
            </dl>
        @endif
        <div class="mt-6 rounded-xl bg-sky-50 p-4 text-sm text-sky-900">
            {{ __('İlgili şikâyetleri yanıtlama ve durum güncelleme işlevleri sıradaki iterasyonda eklenecek.') }}
        </div>
    </section>
@endsection
