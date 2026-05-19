@extends('layouts.app')

@section('title', $campaign->title.' • '.__('Kampanya'))

@section('content')
    @php
        $sup = max(0, (int) $campaign->supporter_count);
        $goal = max(0, (int) ($campaign->goal_supporters ?? 0));
        $progressPct = $goal > 0 ? min(100, (int) round(100 * $sup / $goal)) : null;
        $tickerSupporters = $campaignSupporters ?? collect();
        $descriptionParagraphs = preg_split('/\R{2,}/', (string) $campaign->description) ?: [];
    @endphp

    <div class="mx-auto max-w-6xl">
        <nav class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('campaigns.index') }}"
                class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-bold text-neutral-800 shadow-sm transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-950">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                </svg>
                {{ __('Tüm kampanyalar') }}
            </a>
            @if ($campaign->topic)
                <a href="{{ route('campaigns.index', ['konu' => $campaign->topic->id]) }}"
                    class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-bold text-sky-900 hover:bg-sky-100">
                    {{ $campaign->topic->name }}
                </a>
            @endif
        </nav>

        @unless ($campaign->isPubliclyApproved())
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-950">
                <p class="font-bold">{{ __('Yayın durumu') }}: {{ $campaign->moderation_status->label() }}</p>
                @if ($campaign->moderation_note && $campaign->moderation_status === \App\Enums\CampaignModerationStatus::Rejected)
                    <p class="mt-2 font-medium">{{ __('Gerekçe: :note', ['note' => $campaign->moderation_note]) }}</p>
                @elseif ($campaign->moderation_status === \App\Enums\CampaignModerationStatus::Unpublished)
                    <p class="mt-2 font-medium">{{ __('Bu kampanya yayından kaldırıldı; yalnızca sana ve yöneticilere görünür.') }}</p>
                    @if ($campaign->moderation_note)
                        <p class="mt-1 text-amber-900/90">{{ __('Not: :note', ['note' => $campaign->moderation_note]) }}</p>
                    @endif
                @elseif ($campaign->moderation_status === \App\Enums\CampaignModerationStatus::Pending)
                    <p class="mt-2 font-medium">{{ __('Onay bekleniyor; şu an yalnızca sen ve yöneticiler görebilirsiniz.') }}</p>
                @endif
            </div>
        @endunless

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem] xl:grid-cols-[minmax(0,1fr)_22rem] xl:gap-10">
            {{-- Ana içerik --}}
            <div class="min-w-0 space-y-6">
                @if ($campaign->hero_image_url)
                    <figure class="overflow-hidden rounded-3xl border border-neutral-200 bg-neutral-100 shadow-sm ring-1 ring-black/[0.04]">
                        <img src="{{ $campaign->hero_image_url }}" alt=""
                            class="aspect-[16/9] w-full object-cover sm:aspect-[2.1/1]" loading="eager" decoding="async">
                    </figure>
                @endif

                <article class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm ring-1 ring-black/[0.03] sm:p-9">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-orange-700">{{ __('Sosyal sorumluluk kampanyası') }}</p>

                    <h1 class="mt-3 font-heading text-[clamp(1.65rem,4vw,2.35rem)] font-black leading-[1.12] tracking-tight text-neutral-950">
                        {{ $campaign->title }}
                    </h1>

                    @if (trim((string) ($campaign->excerpt ?? '')) !== '')
                        <p class="mt-4 text-lg font-semibold leading-relaxed text-neutral-800">
                            {{ $campaign->excerpt }}
                        </p>
                    @endif

                    <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-neutral-100 pt-5 text-sm font-semibold text-neutral-700">
                        @if ($campaign->user)
                            <span class="inline-flex items-center gap-2">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-amber-500 text-xs font-black text-white ring-2 ring-white shadow-sm">
                                    {{ $campaign->user->avatarInitials() }}
                                </span>
                                <span>{{ $campaign->user->name }}</span>
                            </span>
                        @endif
                        @if ($campaign->city)
                            <span class="text-neutral-400" aria-hidden="true">•</span>
                            <span>{{ $campaign->city->name }}</span>
                        @elseif ($campaign->city_id === null)
                            <span class="text-neutral-400" aria-hidden="true">•</span>
                            <span>{{ __('Türkiye geneli') }}</span>
                        @endif
                        @if ($campaign->ends_at)
                            <span class="text-neutral-400" aria-hidden="true">•</span>
                            <time datetime="{{ $campaign->ends_at->toIso8601String() }}">
                                {{ __('Bitiş') }}: {{ $campaign->ends_at->translatedFormat('d M Y') }}
                            </time>
                        @endif
                    </div>

                    <div class="mt-8 space-y-5 border-t border-neutral-100 pt-8">
                        @forelse ($descriptionParagraphs as $para)
                            @if (trim((string) $para) !== '')
                                <p class="text-[16px] leading-[1.75] text-neutral-800">{{ trim((string) $para) }}</p>
                            @endif
                        @empty
                            <p class="text-[16px] leading-relaxed text-neutral-600">{{ __('Kampanya açıklaması henüz eklenmemiş.') }}</p>
                        @endforelse
                    </div>
                </article>

                @if ($campaignComments)
                    <section id="yorumlar" class="scroll-mt-24 rounded-3xl border border-neutral-200 bg-white shadow-sm ring-1 ring-black/[0.03]">
                        <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 sm:px-7">
                            <h2 class="text-lg font-black text-neutral-950">{{ __('Yorumlar') }}</h2>
                            <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-sm font-bold tabular-nums text-neutral-700">
                                {{ $campaignComments->total() }}
                            </span>
                        </div>

                        <ul class="divide-y divide-neutral-100 px-5 sm:px-7">
                            @forelse ($campaignComments as $comment)
                                @php($author = $comment->user)
                                <li class="flex gap-3 py-4">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-amber-500 text-[10px] font-black text-white ring-2 ring-white shadow-sm">
                                        {{ $author ? $author->avatarInitials() : '?' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[15px] leading-snug text-neutral-900">
                                            @if ($author)
                                                <span class="font-bold">{{ $author->name }}</span>
                                            @else
                                                <span class="font-bold text-neutral-500">{{ __('Kullanıcı') }}</span>
                                            @endif
                                            <span class="font-normal text-neutral-800"> {{ $comment->content }}</span>
                                        </p>
                                        <p class="mt-1 text-xs font-medium text-neutral-500">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="py-10 text-center text-sm font-medium text-neutral-600">
                                    {{ __('Henüz yorum yok — ilk destek mesajını sen yaz.') }}
                                </li>
                            @endforelse
                        </ul>

                        @if ($campaignComments->hasPages())
                            <div class="border-t border-neutral-100 px-5 py-4 sm:px-7">
                                {{ $campaignComments->links() }}
                            </div>
                        @endif

                        @auth
                            <div class="border-t border-neutral-100 bg-neutral-50/60 px-5 py-4 sm:px-7 sm:py-5">
                                <form method="POST" action="{{ route('campaigns.comments.store', $campaign) }}" class="flex gap-3">
                                    @csrf
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-neutral-800 text-[10px] font-black text-white">
                                        {{ auth()->user()->avatarInitials() }}
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-end">
                                        <label class="sr-only" for="kampanya-yorum">{{ __('Yorum') }}</label>
                                        <textarea id="kampanya-yorum" name="content" rows="2" required maxlength="2000"
                                            placeholder="{{ __('Yorum ekle…') }}"
                                            class="min-h-[48px] w-full resize-y rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-[15px] leading-snug text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-200/80 @error('content') border-rose-300 ring-2 ring-rose-100 @enderror">{{ old('content') }}</textarea>
                                        <button type="submit"
                                            class="inline-flex h-10 shrink-0 items-center justify-center rounded-full bg-amber-400 px-6 text-sm font-black text-neutral-900 hover:bg-amber-300">
                                            {{ __('Paylaş') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <p class="border-t border-neutral-100 px-5 py-4 text-center text-sm font-medium text-neutral-700 sm:px-7">
                                <a href="{{ route('login') }}" class="font-bold text-orange-700 underline-offset-2 hover:underline">{{ __('Giriş yap') }}</a>
                                {{ __('— yorum yazmak için.') }}
                            </p>
                        @endauth
                    </section>
                @endif
            </div>

            {{-- Kenar çubuğu --}}
            <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
                @if ($campaign->isPubliclyApproved())
                    <div class="rounded-3xl border border-orange-200/80 bg-gradient-to-b from-orange-50 to-white p-5 shadow-sm ring-1 ring-orange-100">
                        <p class="text-[11px] font-black uppercase tracking-[0.16em] text-orange-800">{{ __('Destekçi sayısı') }}</p>
                        <p class="mt-2 font-heading text-4xl font-black tabular-nums leading-none text-orange-600">
                            {{ number_format($sup) }}
                        </p>
                        @if ($goal > 0)
                            <div class="mt-4">
                                <div class="flex items-center justify-between gap-2 text-xs font-bold text-neutral-700">
                                    <span>{{ __('Hedef') }} {{ number_format($goal) }}</span>
                                    <span class="tabular-nums text-orange-800">{{ $progressPct }}%</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-orange-100">
                                    <div class="h-full rounded-full bg-orange-500 transition-all" style="width: {{ $progressPct }}%"></div>
                                </div>
                            </div>
                        @endif
                        <p class="mt-3 text-xs font-semibold tabular-nums text-neutral-600">
                            {{ number_format(max(0, (int) $campaign->view_count)) }} {{ __('görüntülenme') }}
                        </p>

                        <div class="mt-5 border-t border-orange-200/60 pt-5">
                            @auth
                                <form method="POST" action="{{ route('campaigns.support.web', $campaign) }}">
                                    @csrf
                                    @if ($campaign->ends_at && $campaign->ends_at->isPast())
                                        <p class="text-sm font-semibold text-rose-800">{{ __('Kampanya süresi dolmuş.') }}</p>
                                    @else
                                        <button type="submit"
                                            class="flex w-full min-h-[48px] items-center justify-center gap-2 rounded-full px-6 text-sm font-black shadow-md transition {{ ! empty($campaign->viewer_supports) ? 'bg-neutral-900 text-white hover:bg-neutral-800' : 'bg-amber-400 text-neutral-900 hover:bg-amber-300' }}">
                                            @if (! empty($campaign->viewer_supports))
                                                {{ __('Desteği geri al') }}
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                                    <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.003-.002.001h-.002Z" />
                                                </svg>
                                                {{ __('Destek ol') }}
                                            @endif
                                        </button>
                                    @endif
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex w-full min-h-[48px] items-center justify-center rounded-full bg-amber-400 px-6 text-sm font-black text-neutral-900 shadow-md hover:bg-amber-300">
                                    {{ __('Destek için giriş yap') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                @endif

                @if ($tickerSupporters->isNotEmpty())
                    <section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-sm ring-1 ring-black/[0.03]" aria-label="{{ __('Son destekçiler') }}">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-black text-neutral-950">{{ __('Son destekçiler') }}</h2>
                            <span class="rounded-full bg-orange-100 px-2 py-0.5 text-xs font-bold tabular-nums text-orange-900">
                                {{ number_format($tickerSupporters->count()) }}
                            </span>
                        </div>
                        <ul class="mt-4 flex max-h-52 flex-wrap gap-2 overflow-y-auto pr-0.5 [scrollbar-width:thin]">
                            @foreach ($tickerSupporters as $supporterRow)
                                @php($u = $supporterRow->user)
                                @continue(!$u)
                                <li>
                                    <span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1.5 text-xs font-semibold text-neutral-900">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 text-[9px] font-black text-white"
                                            aria-hidden="true">{{ $u->avatarInitials() }}</span>
                                        <span class="max-w-[9rem] truncate">{{ $u->name }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @elseif ($campaign->isPubliclyApproved() && $sup === 0)
                    <p class="rounded-2xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3 text-center text-sm font-medium text-orange-950">
                        {{ __('Henüz kimse desteklemedi — ilk sen ol!') }}
                    </p>
                @endif

                <dl class="rounded-3xl border border-neutral-200 bg-white p-5 text-sm shadow-sm ring-1 ring-black/[0.03]">
                    <div class="flex justify-between gap-3 border-b border-neutral-100 py-2.5 first:pt-0">
                        <dt class="font-semibold text-neutral-600">{{ __('Görüntülenme') }}</dt>
                        <dd class="font-bold tabular-nums text-neutral-950">{{ number_format(max(0, (int) $campaign->view_count)) }}</dd>
                    </div>
                    @if ($campaign->city)
                        <div class="flex justify-between gap-3 border-b border-neutral-100 py-2.5">
                            <dt class="font-semibold text-neutral-600">{{ __('İl') }}</dt>
                            <dd class="font-bold text-neutral-950">{{ $campaign->city->name }}</dd>
                        </div>
                    @endif
                    @if ($campaign->ends_at)
                        <div class="flex justify-between gap-3 py-2.5">
                            <dt class="font-semibold text-neutral-600">{{ __('Bitiş') }}</dt>
                            <dd class="text-right font-bold text-neutral-950">{{ $campaign->ends_at->translatedFormat('d M Y, H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </aside>
        </div>
    </div>
@endsection
