@extends('layouts.app')

@section('toolbar')
    <div></div>
@endsection

@section('title', $campaign->title.' • '.__('Kampanya'))

@section('content')
    <article class="overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-indigo-100">
        @if ($campaign->hero_image_url)
            <div class="relative aspect-[2.35/1] w-full bg-neutral-900">
                <img src="{{ $campaign->hero_image_url }}" alt="" class="h-full w-full object-cover opacity-90" loading="eager">
                <span
                    class="absolute bottom-5 left-5 rounded-full bg-indigo-600/92 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white shadow-lg">{{ __('Sosyal sorumluluk') }}</span>
            </div>
        @endif
        <div class="border-b border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-violet-50 px-6 py-6 sm:px-10 sm:py-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    @unless ($campaign->isPubliclyApproved())
                        <p class="rounded-full bg-amber-100 px-3 py-1 text-[11px] font-black uppercase tracking-wider text-amber-950 ring-2 ring-amber-200">{{ $campaign->moderation_status->label() }}</p>
                        @if ($campaign->moderation_note && $campaign->moderation_status === \App\Enums\CampaignModerationStatus::Rejected)
                            <p class="mt-2 text-sm font-semibold text-rose-800">{{ __('Gerekçe: :note', ['note' => $campaign->moderation_note]) }}</p>
                        @elseif ($campaign->moderation_status === \App\Enums\CampaignModerationStatus::Pending)
                            <p class="mt-2 text-sm font-semibold text-amber-900">{{ __('Bu kampanya yalnızca sana ve yöneticilere görünüyor. Süper yönetici onayından sonra herkese açılacak.') }}</p>
                        @endif
                    @endunless
                    <h1 class="mt-3 text-[clamp(1.4rem,2.9vw,2.1rem)] font-black tracking-tight text-neutral-950">{{ $campaign->title }}</h1>
                    @if (trim((string) ($campaign->excerpt ?? '')) !== '')
                        <p class="mt-4 text-[17px] font-semibold leading-relaxed text-neutral-800">{{ $campaign->excerpt }}</p>
                    @endif
                </div>
                <div class="shrink-0 rounded-2xl bg-white px-5 py-4 text-center shadow-md ring-2 ring-indigo-100">
                    <p class="text-[11px] font-black uppercase tracking-wider text-neutral-600">{{ __('Destekçiler') }}</p>
                    <p class="mt-2 text-[2rem] font-black tabular-nums text-indigo-700">{{ number_format(max(0, (int) $campaign->supporter_count)) }}</p>
                    @if ($campaign->goal_supporters)
                        <p class="mt-2 text-[12px] font-bold text-neutral-600">{{ __('Hedef') }} {{ number_format((int) $campaign->goal_supporters) }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="whitespace-pre-line px-6 py-8 text-[17px] font-medium leading-relaxed text-neutral-800 sm:px-10">
            {{ $campaign->description }}
        </div>
        <div class="border-t border-neutral-100 px-6 py-8 sm:px-10">
            @if ($campaign->isPubliclyApproved())
                <div class="flex flex-wrap items-center gap-4">
                    @auth
                        <form method="POST" action="{{ route('campaigns.support.web', $campaign) }}" class="inline">
                            @csrf
                            @if ($campaign->ends_at && $campaign->ends_at->isPast())
                                <p class="text-sm font-semibold text-rose-800">{{ __('Kampanya süresi dolmuş.') }}</p>
                            @else
                                <button type="submit"
                                    class="rounded-full px-8 py-3.5 text-[13px] font-black uppercase tracking-widest shadow-lg ring-4 ring-indigo-100 transition {{ ! empty($campaign->viewer_supports) ? 'bg-neutral-900 text-white' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                    {{ ! empty($campaign->viewer_supports) ? __('Desteği geri al') : __('Destek ol') }}</button>
                            @endif
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="rounded-full bg-indigo-600 px-8 py-3.5 text-[13px] font-black text-white hover:bg-indigo-700">{{ __('Destek için giriş yap') }}</a>
                    @endauth
                    <a href="{{ route('campaigns.index') }}" class="text-[13px] font-bold text-indigo-800 underline">{{ __('« Tüm kampanyalar') }}</a>
                </div>
            @endif
            <dl class="mt-6 grid gap-3 text-[13px] font-semibold text-neutral-700 sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] uppercase tracking-wide text-neutral-500">{{ __('Oluşturan') }}</dt>
                    <dd class="mt-0.5">{{ $campaign->user?->name }}</dd>
                </div>
                @if ($campaign->city)
                    <div>
                        <dt class="text-[11px] uppercase tracking-wide text-neutral-500">{{ __('İl odaklı') }}</dt>
                        <dd class="mt-0.5">{{ $campaign->city->name }}</dd>
                    </div>
                @endif
                @if ($campaign->ends_at)
                    <div>
                        <dt class="text-[11px] uppercase tracking-wide text-neutral-500">{{ __('Bitiş') }}</dt>
                        <dd class="mt-0.5">{{ $campaign->ends_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </article>
@endsection
