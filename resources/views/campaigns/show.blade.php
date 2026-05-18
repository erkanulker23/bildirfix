@extends('layouts.app')

@section('title', $campaign->title.' • '.__('Kampanya'))

@section('content')
    <article
        class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-[0_22px_48px_-28px_rgba(15,23,42,0.18)]">
        @if ($campaign->hero_image_url)
            <div class="relative aspect-[2.35/1] w-full bg-neutral-900">
                <img src="{{ $campaign->hero_image_url }}" alt="" class="h-full w-full object-cover" loading="eager">
                <span
                    class="absolute bottom-5 left-5 rounded-full border border-white/35 bg-neutral-950 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-white shadow-sm">{{ __('Sosyal sorumluluk') }}</span>
            </div>
        @endif

        <div class="relative border-b border-neutral-200 bg-white px-5 py-8 sm:px-10 sm:py-10">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between lg:gap-10">
                <div class="min-w-0 flex-1 space-y-5">
                    @unless ($campaign->isPubliclyApproved())
                        <p
                            class="inline-flex rounded-full border border-amber-200/90 bg-amber-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-950">
                            {{ $campaign->moderation_status->label() }}</p>
                        @if ($campaign->moderation_note && $campaign->moderation_status === \App\Enums\CampaignModerationStatus::Rejected)
                            <p class="text-sm font-medium leading-relaxed text-rose-800">{{ __('Gerekçe: :note', ['note' => $campaign->moderation_note]) }}</p>
                        @elseif ($campaign->moderation_status === \App\Enums\CampaignModerationStatus::Unpublished)
                            <p class="text-sm font-medium leading-relaxed text-neutral-700">{{ __('Bu kampanya yönetici tarafından yayından kaldırıldı; yalnızca sana ve yöneticilere görünür.') }}</p>
                            @if ($campaign->moderation_note)
                                <p class="text-sm font-medium leading-relaxed text-neutral-600">{{ __('Not: :note', ['note' => $campaign->moderation_note]) }}</p>
                            @endif
                        @elseif ($campaign->moderation_status === \App\Enums\CampaignModerationStatus::Pending)
                            <p class="text-sm font-medium leading-relaxed text-amber-950/90">{{ __('Bu kampanya yalnızca sana ve yöneticilere görünüyor. Süper yönetici onayından sonra herkese açılacak.') }}</p>
                        @endif
                    @endunless

                    <header class="space-y-4">
                        @if ($campaign->topic)
                            <a href="{{ route('campaigns.index', ['konu' => $campaign->topic->id]) }}"
                                class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-bold text-sky-900 hover:bg-sky-100">
                                {{ $campaign->topic->name }}
                            </a>
                        @endif
                        <h1
                            class="font-heading text-[clamp(1.65rem,4vw,2.35rem)] font-black leading-[1.12] tracking-tight text-[#0a0a0a]">
                            {{ $campaign->title }}</h1>
                        @if (trim((string) ($campaign->excerpt ?? '')) !== '')
                            <p class="max-w-3xl text-lg font-semibold leading-relaxed text-[#1e293b] sm:text-[1.125rem]">
                                {{ $campaign->excerpt }}</p>
                        @endif
                    </header>
                </div>

                <div
                    class="flex w-full shrink-0 flex-col items-center justify-center rounded-2xl border border-neutral-200 bg-white px-6 py-5 text-center shadow-sm sm:min-w-[158px] lg:w-auto lg:self-start">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-neutral-500">{{ __('Destekçiler') }}</p>
                    <p class="mt-2 font-heading text-[2.25rem] font-black tabular-nums leading-none text-indigo-700">
                        {{ number_format(max(0, (int) $campaign->supporter_count)) }}</p>
                    @if ($campaign->goal_supporters)
                        <p class="mt-3 border-t border-neutral-100 pt-3 text-[13px] font-semibold tabular-nums text-neutral-600">
                            {{ __('Hedef') }} <span class="text-neutral-900">{{ number_format((int) $campaign->goal_supporters) }}</span>
                        </p>
                    @endif
                </div>
            </div>

            @if ($campaign->user)
                <div
                    class="mt-8 flex flex-wrap items-center gap-4 rounded-2xl border border-neutral-200 bg-white px-5 py-4 shadow-sm">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-violet-600 text-[13px] font-black tracking-wide text-white shadow-inner ring-2 ring-neutral-100">
                        {{ $campaign->user->avatarInitials() }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-neutral-500">{{ __('Kampanyayı oluşturdu') }}</p>
                        <p class="mt-1 truncate font-heading text-lg font-extrabold text-neutral-950">{{ $campaign->user->name }}</p>
                    </div>
                </div>
            @endif

            @php
                $tickerSupporters = $campaignSupporters ?? collect();
            @endphp
            @if ($tickerSupporters->isNotEmpty())
                <section class="mt-8" aria-label="{{ __('Son destekçiler') }}">
                    <p class="sr-only">
                        {{ $tickerSupporters->map(fn ($row) => trim(($row->user?->name ?? '').' '.__('destekledi')))->filter()->take(40)->implode('. ') }}
                    </p>
                    <div
                        class="overflow-hidden rounded-2xl border border-violet-400/30 bg-gradient-to-br from-[#0f0a1a] via-neutral-900 to-[#0a1628] p-4 shadow-[0_12px_40px_-16px_rgba(91,33,182,0.45)] sm:p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-3">
                            <p class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-300/90">{{ __('Destek halkası') }}</p>
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-bold tabular-nums text-white ring-1 ring-white/15">{{ number_format($tickerSupporters->count()) }}</span>
                        </div>
                        <div class="mt-4 flex max-h-[9.5rem] flex-wrap gap-2 overflow-y-auto pr-1 [scrollbar-width:thin] sm:max-h-[11rem]">
                            @foreach ($tickerSupporters as $supporterRow)
                                @php($u = $supporterRow->user)
                                @continue(!$u)
                                <span
                                    class="inline-flex items-center gap-2 rounded-xl border border-emerald-400/25 bg-white/5 px-3 py-2 text-[12px] leading-snug text-white shadow-sm ring-1 ring-inset ring-white/5">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-600 text-[10px] font-black text-neutral-950"
                                        aria-hidden="true">{{ $u->avatarInitials() }}</span>
                                    <span class="max-w-[10rem] truncate font-semibold">{{ $u->name }}</span>
                                </span>
                            @endforeach
                        </div>
                        <p class="mt-3 text-center text-[11px] font-medium text-white/70">{{ __('Kampanyaya son katılan destekçiler') }}</p>
                    </div>
                </section>
            @elseif ($campaign->isPubliclyApproved() && (int) $campaign->supporter_count === 0)
                <p class="mt-8 rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3.5 text-center text-[13px] font-medium leading-relaxed text-neutral-600">
                    {{ __('Henüz kimse desteklemedi — ilk sen ol!') }}</p>
            @endif
        </div>

        <div class="px-5 py-9 sm:px-10 sm:py-11">
            <div class="mx-auto max-w-3xl sm:mx-0">
                <div class="whitespace-pre-line text-[17px] font-normal leading-[1.78] text-neutral-800">
                    {{ $campaign->description }}</div>
            </div>
        </div>

        @if ($campaignComments)
            <section id="yorumlar" class="scroll-mt-24 border-t border-neutral-200 bg-neutral-50 px-5 py-8 sm:px-10 sm:py-9">
                <div class="mx-auto max-w-md">
                    <div class="flex items-center justify-between gap-3 border-b border-neutral-200/90 pb-3">
                        <h2 class="text-[15px] font-bold text-neutral-950">{{ __('Yorumlar') }}</h2>
                        <span
                            class="tabular-nums text-[13px] font-semibold text-neutral-500">{{ $campaignComments->total() }}</span>
                    </div>

                    <div class="max-h-[min(520px,55vh)] overflow-y-auto overscroll-contain [-webkit-overflow-scrolling:touch]">
                        <ul class="divide-y divide-neutral-100">
                            @forelse ($campaignComments as $comment)
                                @php($author = $comment->user)
                                <li class="flex gap-3 py-3.5">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-[10px] font-black uppercase tracking-wide text-white ring-2 ring-white shadow-sm">
                                        {{ $author ? $author->avatarInitials() : '?' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[14px] leading-snug text-neutral-900">
                                            @if ($author)
                                                <span class="font-semibold">{{ $author->name }}</span>
                                            @else
                                                <span class="font-semibold text-neutral-500">{{ __('Kullanıcı') }}</span>
                                            @endif
                                            <span class="whitespace-pre-wrap font-normal text-neutral-800"> {{ $comment->content }}</span>
                                        </p>
                                        <p class="mt-1 text-[12px] font-medium text-neutral-400">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center text-[13px] font-medium text-neutral-500">
                                    {{ __('Henüz yorum yok — ilk destek mesajını sen yaz.') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    @if ($campaignComments->hasPages())
                        <div class="mt-4 flex justify-center border-t border-neutral-100 pt-4">
                            {{ $campaignComments->links() }}</div>
                    @endif

                    @auth
                        <div class="mt-5 border-t border-neutral-200/80 pt-4">
                            <form method="POST" action="{{ route('campaigns.comments.store', $campaign) }}"
                                class="flex gap-3">
                                @csrf
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-neutral-800 to-neutral-600 text-[10px] font-black text-white ring-2 ring-neutral-100">
                                    {{ auth()->user()->avatarInitials() }}</div>
                                <div class="flex min-w-0 flex-1 flex-col gap-2.5 sm:flex-row sm:items-end">
                                    <label class="sr-only" for="kampanya-yorum">{{ __('Yorum') }}</label>
                                    <textarea id="kampanya-yorum" name="content" rows="2" required maxlength="2000"
                                        placeholder="{{ __('Yorum ekle…') }}"
                                        class="min-h-[48px] w-full resize-y rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-[14px] leading-snug text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200/80 @error('content') border-rose-300 ring-2 ring-rose-100 @enderror">{{ old('content') }}</textarea>
                                    <button type="submit"
                                        class="inline-flex h-10 shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-indigo-700 sm:h-10">{{ __('Paylaş') }}</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <p class="mt-5 border-t border-neutral-200/80 pt-4 text-center text-[13px] font-medium text-neutral-600">
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-700 underline-offset-2 hover:underline">{{ __('Giriş yap') }}</a>
                            {{ __('— destek yorumu yazmak için.') }}</p>
                    @endauth
                </div>
            </section>
        @endif

        <div class="border-t border-neutral-200 bg-neutral-50/40 px-5 py-9 sm:px-10 sm:py-10">
            @if ($campaign->isPubliclyApproved())
                <div class="flex flex-col gap-5 sm:flex-row sm:flex-wrap sm:items-center sm:gap-6">
                    @auth
                        <form method="POST" action="{{ route('campaigns.support.web', $campaign) }}" class="inline">
                            @csrf
                            @if ($campaign->ends_at && $campaign->ends_at->isPast())
                                <p class="text-sm font-medium text-rose-800">{{ __('Kampanya süresi dolmuş.') }}</p>
                            @else
                                <button type="submit"
                                    class="btn-primary min-h-[52px] px-8 py-3.5 text-[15px] font-bold shadow-md transition {{ ! empty($campaign->viewer_supports) ? '!bg-neutral-900 !shadow-none hover:!bg-neutral-800' : '' }}">
                                    @if (! empty($campaign->viewer_supports))
                                        {{ __('Desteği geri al') }}
                                    @else
                                        <span class="inline-flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="h-5 w-5 shrink-0 text-white/90" aria-hidden="true">
                                                <path
                                                    d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.003-.002.001h-.002Z" />
                                            </svg>
                                            {{ __('Destek ol') }}
                                        </span>
                                    @endif
                                </button>
                            @endif
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary min-h-[52px] px-8 py-3.5 text-[15px] font-bold shadow-md">{{ __('Destek için giriş yap') }}</a>
                    @endauth
                    <a href="{{ route('campaigns.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-[14px] font-semibold text-neutral-800 shadow-sm transition hover:border-neutral-400 hover:bg-neutral-50">{{ __('« Tüm kampanyalar') }}</a>
                </div>
            @endif
            <dl class="mt-8 grid gap-5 border-t border-neutral-200/80 pt-8 text-[14px] font-medium text-neutral-700 sm:grid-cols-2">
                @if ($campaign->city)
                    <div class="rounded-xl border border-neutral-200/90 bg-white px-4 py-3 shadow-sm">
                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-neutral-500">{{ __('İl odaklı') }}</dt>
                        <dd class="mt-1 font-heading font-bold text-neutral-950">{{ $campaign->city->name }}</dd>
                    </div>
                @endif
                @if ($campaign->ends_at)
                    <div class="rounded-xl border border-neutral-200/90 bg-white px-4 py-3 shadow-sm">
                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-neutral-500">{{ __('Bitiş') }}</dt>
                        <dd class="mt-1 font-semibold text-neutral-900">{{ $campaign->ends_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </article>
@endsection
