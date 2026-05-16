{{-- Lucide-style SVG (inline). Veri: Post modeli + viewer_supported (isteğe bağlı). --}}
@props([
    'post',
])

@php
    use App\Enums\PostStatus;
    use App\Enums\VerificationStatus;
    use App\Support\PostMediaPresenter;

    $pm = PostMediaPresenter::primary($post);
    $mediaUrl = $pm['url'] ?? null;
    $mediaType = $pm['type'] ?? 'image';
    $poster = $pm['poster'] ?? null;

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
        {{-- Header --}}
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
                <div class="min-w-0">
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
        </div>

        @if ($mediaUrl)
            <div class="relative w-full overflow-hidden bg-gray-100 aspect-video">
                @if ($mediaType === 'video')
                    @if ($poster)
                        <img src="{{ $poster }}" alt="" class="h-full w-full object-cover opacity-90" loading="lazy">
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center bg-black/35">
                        <span
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-black/50 backdrop-blur-sm">
                            <svg class="ml-0.5 h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor"
                                aria-hidden="true">
                                <polygon points="6 4 20 12 6 20 6 4" />
                            </svg>
                        </span>
                    </div>
                @else
                    <img src="{{ $mediaUrl }}" alt="{{ $post->title }}" loading="lazy"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.02]">
                @endif
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2 px-4 pt-3">
            @if ($post->category)
                <span class="badge badge-category text-xs">{{ $post->category->name }}</span>
            @endif
            <span class="badge {{ $statusClass }} text-xs">{{ $post->status->label() }}</span>
            @php
                $__ibadges = ($post->relationLoaded('institutions') && $post->institutions->isNotEmpty())
                    ? $post->institutions
                    : ($post->institution ? collect([$post->institution]) : collect());
            @endphp
            @foreach ($__ibadges as $__ti)
                <span class="badge bg-gray-100 text-xs text-gray-600">🏛️ {{ $__ti->name }}</span>
            @endforeach
        </div>

        <div class="px-4 pb-3 pt-2">
            <h3 class="font-heading line-clamp-2 text-base font-bold leading-snug text-gray-900">{{ $post->title }}</h3>
            @if ($post->description)
                <p class="mt-1 line-clamp-2 text-sm leading-relaxed text-gray-500">
                    {{ strip_tags((string) $post->description) }}</p>
            @endif
        </div>
    </a>

    {{-- Aksiyonlar (link dışında) --}}
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
