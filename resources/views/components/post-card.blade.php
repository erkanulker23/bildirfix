{{-- Lucide-style SVG (inline). Veri: Post modeli + viewer_supported (isteğe bağlı). --}}
@props([
    'post',
    'compact' => false,
])

@php
    use App\Enums\PostStatus;
    use App\Enums\VerificationStatus;
    use App\Support\PostMediaPresenter;

    $pm = PostMediaPresenter::primary($post);
    $hasMedia = $pm !== null;
    $mediaType = $pm['type'] ?? 'image';

    $statusClass = match ($post->status) {
        PostStatus::Open => 'badge-open',
        PostStatus::InProgress => 'badge-progress',
        PostStatus::Resolved => 'badge-resolved',
        PostStatus::Rejected => 'badge-rejected',
    };

    $uname = trim((string) ($post->user?->name ?? '?'));
    preg_match_all('/\p{L}/u', $uname, $m);
    $ini = (($m[0] ?? []) !== []) ? mb_strtoupper(implode('', array_slice($m[0], 0, 2))) : mb_strtoupper(mb_substr($uname, 0, 2));
    $ini = mb_substr($ini ?: '?', 0, 2);

    $verified = $post->user && ($post->user->verification_status ?? null) === VerificationStatus::Verified;
    $viewerSupported = auth()->check() && ! empty($post->viewer_supported);

    $locLine = collect([$post->district?->name, $post->city?->name])->filter()->implode(', ');
    if ($locLine === '') {
        $locLine = __('Konum belirtilmedi');
    }
@endphp

<article class="card-post group">
    <a href="{{ route('posts.show', $post) }}"
        class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
        <div class="flex items-start justify-between gap-2 px-4 pb-3 pt-4">
            <div class="flex min-w-0 flex-1 items-center gap-3">
                <div class="relative shrink-0">
                    <span
                        class="font-heading flex h-11 w-11 items-center justify-center rounded-full bg-secondary-soft text-sm font-bold text-white ring-2 ring-gray-100">
                        {{ $ini }}
                    </span>
                    @if ($verified)
                        <span
                            class="absolute -bottom-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-info ring-2 ring-white"
                            aria-hidden="true">
                            <svg class="h-2.5 w-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                        </span>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="truncate text-sm font-semibold leading-none text-gray-900">{{ $uname }}</span>
                        @if ($verified)
                            <span class="text-xs font-medium text-info" title="{{ __('Doğrulanmış') }}">✓</span>
                        @endif
                    </div>
                    <div class="mt-1 flex flex-wrap items-center gap-1 text-xs text-gray-400">
                        <svg class="h-3 w-3 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span class="truncate">{{ $locLine }}</span>
                        <span class="text-gray-300" aria-hidden="true">•</span>
                        <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
                    </div>
                </div>
            </div>
            @if ($hasMedia)
                <span
                    class="inline-flex shrink-0 items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-violet-800 ring-1 ring-violet-100"
                    title="{{ __('Medya detay sayfasında') }}">
                    {{ $mediaType === 'video' ? __('Video') : __('Fotoğraf') }}
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2 px-4 {{ $compact ? 'pt-0' : 'pt-1' }}">
            @if ($post->category)
                <span class="badge badge-category text-xs">{{ $post->category->name }}</span>
            @endif
            <span class="badge {{ $statusClass }} text-xs">{{ $post->status->label() }}</span>
            @if ($hasMedia)
                <span class="badge bg-violet-50 text-xs text-violet-800 ring-1 ring-violet-100">
                    {{ $mediaType === 'video' ? __('Videolu') : __('Fotoğraflı') }}
                </span>
            @endif
            @php
                $__ibadges = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
                    ? $post->institutions
                    : ($post->institution ? collect([$post->institution]) : collect());
            @endphp
            @foreach ($__ibadges->take(2) as $__ti)
                <span class="badge bg-gray-100 text-xs text-gray-600">🏛️ {{ \Illuminate\Support\Str::limit($__ti->name, 28) }}</span>
            @endforeach
        </div>

        <div class="px-4 pb-3 pt-2">
            <h3 class="font-heading line-clamp-2 text-base font-bold leading-snug text-gray-900 group-hover:text-primary">{{ $post->title }}</h3>
            @if ($post->description)
                <p class="mt-1 line-clamp-2 text-sm leading-relaxed text-gray-500">
                    {{ strip_tags((string) $post->description) }}</p>
            @endif
            @if ($compact && $hasMedia)
                <p class="mt-2 text-[12px] font-semibold text-violet-700">{{ __('Görseller detay sayfasında →') }}</p>
            @endif
        </div>
    </a>

    <div class="flex items-center justify-between gap-2 border-t border-gray-100 px-4 py-3">
        <div class="flex items-center gap-2 sm:gap-4">
            @auth
                <form method="POST" action="{{ route('posts.support.web', $post) }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="flex min-h-11 min-w-11 items-center gap-1.5 rounded-full px-1 transition-colors hover:bg-primary-light">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full {{ $viewerSupported ? 'bg-primary-light text-primary' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </span>
                        <span class="text-sm font-semibold {{ $viewerSupported ? 'text-primary' : 'text-gray-500' }}">{{ number_format(max(0, (int) $post->support_count)) }}</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="flex min-h-11 items-center gap-1.5 rounded-full px-1 text-sm font-semibold text-gray-500 hover:text-primary">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    {{ number_format(max(0, (int) $post->support_count)) }}
                </a>
            @endauth

            <a href="{{ route('posts.show', $post) }}#yorumlar"
                class="flex min-h-11 min-w-11 items-center gap-1.5 rounded-full px-1 transition-colors hover:bg-info-light">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <span class="text-sm font-semibold text-gray-500">{{ number_format(max(0, (int) $post->comments_count)) }}</span>
            </a>

            <span class="flex min-h-11 items-center gap-1.5 rounded-full px-1 text-sm font-semibold text-gray-500" title="{{ __('Görüntülenme') }}">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </span>
                {{ number_format(max(0, (int) $post->view_count)) }}
            </span>
        </div>

        <button type="button"
            class="flex min-h-11 min-w-11 items-center gap-1.5 rounded-full px-2 text-gray-400 transition-colors hover:text-primary"
            data-post-share="{{ $post->id }}" onclick="window.dsSharePost?.({{ $post->id }})">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="18" cy="5" r="3" />
                <circle cx="6" cy="12" r="3" />
                <circle cx="18" cy="19" r="3" />
                <line x1="8.59" x2="15.41" y1="13.51" y2="17.49" />
                <line x1="15.41" x2="8.59" y1="6.51" y2="10.49" />
            </svg>
            <span class="text-xs font-medium">{{ __('Paylaş') }}</span>
        </button>
    </div>
</article>
