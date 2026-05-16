@extends('layouts.app')

@section('title', __('KVKK bildirimi').' • '.config('app.name'))

@section('content')
    <article class="prose prose-slate prose-sm mx-auto max-w-prose px-4 py-8 prose-headings:font-black prose-p:leading-relaxed">
        <h1>{{ __('KVKK başvuru süreç taslağı') }}</h1>
        <p>{{ __('Veri sorumlusu sıfatına kayıtlı bilgiler, başvuru yolları, saklama süreleri ve ilgili mevzuat atıfları profesyonelce tamamlanmalıdır.') }}</p>
        <h2>{{ __('İlgili kişi hakları') }}</h2>
        <p>{{ __('Kişisel veriler hakkında bilgi talebi, düzeltme, silme ve itiraz yollarının net şekilde anlatılması gerekir.') }}</p>
    </article>
@endsection
