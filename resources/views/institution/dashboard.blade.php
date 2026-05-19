@extends('layouts.panel', ['panelKind' => 'institution'])

@section('title', __('Dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kurum paneli') }}</h1>
                <p class="psc-page-desc">
                    {{ $institution ? $institution->name : __('Kurum profilin henüz atanmadı.') }}
                </p>
            </div>
        </div>

        @if ($institution)
            <div class="psc-metrics sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Toplam şikâyet') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Açık') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['open']) }}</p>
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('İşlemde') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['in_progress']) }}</p>
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Çözüldü') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['resolved']) }}</p>
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Görüntülenme') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['views']) }}</p>
                    </div>
                </div>
                <div class="psc-metric">
                    <div>
                        <p class="psc-metric__label">{{ __('Destek') }}</p>
                        <p class="psc-metric__value">{{ number_format($stats['supports']) }}</p>
                    </div>
                </div>
            </div>

            <div class="psc-split">
                <section class="psc-card">
                    <div class="psc-card__body">
                        <h2 class="psc-card__title">{{ __('Son şikâyetler') }}</h2>
                        @if ($recentPosts->isEmpty())
                            <p class="mt-3 text-sm text-[#64748b]">{{ __('Kurumunuza atanmış yayında şikâyet bulunmuyor.') }}</p>
                        @else
                            <ul class="mt-4 divide-y divide-[#eef2f7]">
                                @foreach ($recentPosts as $post)
                                    <li class="py-3">
                                        <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener" class="font-semibold text-[#0f172a] hover:text-[#ea580c]">{{ \Illuminate\Support\Str::limit($post->title, 52) }}</a>
                                        <p class="mt-0.5 text-xs text-[#64748b]">
                                            {{ $post->city?->name ?? '—' }}
                                            · {{ $post->status->label() }}
                                            · {{ number_format((int) $post->view_count) }} {{ __('görüntülenme') }}
                                            · {{ number_format((int) $post->support_count) }} {{ __('destek') }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </section>

                <section class="psc-card">
                    <div class="psc-card__body">
                        <h2 class="psc-card__title">{{ __('İllere göre') }}</h2>
                        @if ($byCity->isEmpty())
                            <p class="mt-3 text-sm text-[#64748b]">{{ __('Henüz veri yok.') }}</p>
                        @else
                            <ul class="mt-4 space-y-2">
                                @foreach ($byCity as $row)
                                    <li class="flex items-center justify-between gap-2 text-sm">
                                        <span class="font-medium text-[#0f172a]">{{ $row->city_name }}</span>
                                        <span class="tabular-nums font-semibold text-[#64748b]">{{ number_format((int) $row->total) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </section>
            </div>

            @if ($institution->verified)
                <p class="text-sm text-[#64748b]">
                    <a href="{{ route('institutions.show', $institution) }}" target="_blank" rel="noopener" class="font-semibold text-[#ea580c] hover:underline">{{ __('Kurum sayfasını görüntüle') }}</a>
                </p>
            @endif
        @else
            <section class="psc-card">
                <div class="psc-card__body">
                    <p class="text-sm text-[#64748b]">{{ __('Kurum hesabınız henüz bir kuruma bağlanmadı. Yönetici ile iletişime geçin.') }}</p>
                </div>
            </section>
        @endif
    </div>
@endsection
