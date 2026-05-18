@extends('layouts.app')

@section('title', __('Kampanya başlat'))

@section('content')
    @include('partials.campaign-wizard', [
        'formAction' => route('campaigns.store'),
        'cancelUrl' => route('campaigns.index'),
        'submitLabel' => __('Moderasyona gönder'),
        'campaignTopics' => $campaignTopics,
        'topicGroups' => $topicGroups,
    ])
@endsection
