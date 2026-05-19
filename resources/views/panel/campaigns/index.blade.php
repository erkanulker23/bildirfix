@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Kampanyalarım'))

@section('content')
    <div class="space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Kampanyalarım') }}</h1>
                <p class="psc-page-desc">{{ __('Başlattığın kampanyaları yönet, görüntülenme ve destek sayılarını takip et.') }}</p>
            </div>
            <a href="{{ route('campaigns.create') }}" class="psc-btn psc-btn--primary">{{ __('Kampanya başlat') }}</a>
        </div>

        @if ($campaigns->isEmpty())
            <section class="psc-card">
                <div class="psc-card__body text-center">
                    <p class="text-sm text-[#64748b]">{{ __('Henüz kampanya oluşturmadın.') }}</p>
                    <a href="{{ route('campaigns.create') }}" class="psc-btn psc-btn--primary mt-4">{{ __('İlk kampanyayı başlat') }}</a>
                </div>
            </section>
        @else
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($campaigns as $campaign)
                    <article class="psc-card flex flex-col">
                        @if ($campaign->hero_image_url)
                            <img src="{{ $campaign->hero_image_url }}" alt="" class="aspect-[2.2/1] w-full object-cover" loading="lazy">
                        @endif
                        <div class="psc-card__body flex flex-1 flex-col">
                            <div class="flex flex-wrap items-center gap-2">
                                @include('partials.panel-moderation-badge', ['status' => $campaign->moderation_status])
                                @if ($campaign->topic)
                                    <span class="text-xs font-semibold text-[#64748b]">{{ $campaign->topic->name }}</span>
                                @endif
                            </div>
                            <h2 class="mt-2 font-semibold text-[#0f172a]">{{ $campaign->title }}</h2>
                            @if ($campaign->excerpt)
                                <p class="mt-1 line-clamp-2 flex-1 text-sm text-[#64748b]">{{ $campaign->excerpt }}</p>
                            @endif
                            <p class="mt-3 text-xs font-medium text-[#64748b]">
                                {{ number_format((int) $campaign->supporter_count) }} {{ __('destek') }}
                                · {{ number_format((int) $campaign->view_count) }} {{ __('görüntülenme') }}
                                @if ($campaign->city)
                                    · {{ $campaign->city->name }}
                                @endif
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('panel.campaigns.edit', $campaign) }}" class="psc-btn psc-btn--secondary">{{ __('Düzenle') }}</a>
                                @if ($campaign->isPubliclyApproved())
                                    <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" rel="noopener" class="psc-btn psc-btn--ghost">{{ __('Yayında gör') }}</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-4">{{ $campaigns->links() }}</div>
        @endif
    </div>
@endsection
