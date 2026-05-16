@extends('layouts.app')

@section('title', __('Kullanım koşulları').' • '.config('app.name'))

@section('content')
    <article class="prose prose-slate prose-sm mx-auto max-w-prose px-4 py-8 prose-headings:font-black prose-p:leading-relaxed">
        <h1>{{ __('Kullanım koşulları taslağı') }}</h1>
        <p>{{ __('Kent sorunları bildirimin resmi başvuru yerini almadığı, kullanıcıların doğru içerik yüklemekle yükümlü olduğu ve hakaret / kişisel veri ihlali yapmamanın gerektiği maddeleri ekleyin.') }}</p>
        <h2>{{ __('Yasaklı kullanımlar') }}</h2>
        <p>{{ __('Spam, suistimal, yanlış konum bilgisi, üçüncü kişiyi hedef gösterme vb.') }}</p>
    </article>
@endsection
