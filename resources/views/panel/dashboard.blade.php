@extends('layouts.app')

@section('title', __('Kullanıcı paneli'))

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold">{{ __('Hoş geldin, :name', ['name' => $user->name]) }}</h1>
        <p class="mt-2 text-sm text-slate-600">
            {{ __('Rol') }}:
            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800">{{ $user->role->value }}</span>
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('campaigns.index') }}"
                class="inline-flex rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700">{{ __('Toplumsal kampanyalar') }}</a>
            <a href="{{ route('campaigns.create') }}"
                class="inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-indigo-950 ring-2 ring-indigo-200 hover:bg-indigo-50">{{ __('Yeni kampanya') }}</a>
        </div>
        <div class="mt-6 rounded-xl bg-teal-50 p-4 text-sm text-teal-900">
            {{ __('Buradan yakında içerik oluşturma, bildirimler ve profilin yönetilecek.') }}
        </div>
        <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <dt class="text-xs uppercase text-slate-500">{{ __('Puan') }}</dt>
                <dd class="text-lg font-semibold">{{ $user->score }}</dd>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                <dt class="text-xs uppercase text-slate-500">{{ __('Güven') }}</dt>
                <dd class="text-lg font-semibold">{{ $user->trust_score }}</dd>
            </div>
        </dl>

        @if (!$user->managedInstitution)
            <p class="mt-6 text-xs text-slate-500">
                REST: {{ __('POST') }} /api/v1/posts — Authorization: Bearer &lt;sanctum&gt;
            </p>
        @endif
    </section>
@endsection
