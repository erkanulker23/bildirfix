@extends('layouts.app')

@section('title', __('Gizlilik').' • '.config('app.name'))

@section('content')
    <article class="prose prose-slate prose-sm mx-auto max-w-prose px-4 py-8 prose-headings:font-black prose-p:leading-relaxed">
        <h1>{{ __('Gizlilik politikası özet taslağı') }}</h1>
        <p>{{ __('Kent sorunu bildirenlerin fotoğraf, konum ve iletişim verilerinin nasıl kullanılabileceğini yazılı bir politika olarak yayımlamanız beklenir. Bu sayfa taslağıdır; hukuk uzmanına danışılarak özelleştirin.') }}</p>
        <h2>{{ __('Toplanan veriler') }}</h2>
        <p>{{ __('Hesap, bildirilen kayıtların içeriği, moderasyon günlükleri ve (varsa) cihaz bilgisi.') }}</p>
        <h2>{{ __('Amaç') }}</h2>
        <p>{{ __('Kent sorununu çözmeye yönlendirmek, moderasyon yapmak ve yasal yükümlülükleri yerine getirmek.') }}</p>
    </article>
@endsection
