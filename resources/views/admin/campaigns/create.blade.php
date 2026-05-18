@extends('layouts.admin')

@section('admin_heading', __('Kampanya oluştur'))
@section('title', __('Yeni kampanya'))

@section('content')
    <div class="mx-auto max-w-xl py-4">
        <div class="mb-6">
            <a href="{{ route('admin.campaigns.registry') }}" class="text-xs font-bold text-blue-600 hover:underline">← {{ __('Kampanya listesi') }}</a>
            <h1 class="mt-2 text-xl font-extrabold text-slate-900">{{ __('Kampanya oluştur') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Change.org tarzı adımlarla amaç, hikâye ve kapsam belirlenir.') }}</p>
        </div>

        @include('partials.campaign-wizard', [
            'formAction' => route('admin.campaigns.store'),
            'cancelUrl' => route('admin.campaigns.registry'),
            'submitLabel' => __('Kampanyayı oluştur'),
            'campaignTopics' => $campaignTopics,
            'topicGroups' => $topicGroups,
        ])
    </div>
@endsection
