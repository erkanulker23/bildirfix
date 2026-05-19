@props([
    'overline' => null,
    'title' => '',
    'titleAccent' => null,
    'description' => null,
])

<section
    class="relative border-b border-neutral-200/80 bg-gradient-to-b from-[#faf8f5] via-[#faf8f5] to-[#f3f1ee] px-4 py-12 sm:px-8 sm:py-14 lg:py-16"
    aria-labelledby="page-hero-title">
    <div class="mx-auto max-w-[1200px]">
        @if (filled($overline))
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-[#d97706]">{{ $overline }}</p>
        @endif
        <h1 id="page-hero-title"
            class="mt-3 font-heading text-[clamp(2rem,4.8vw,3.35rem)] font-black leading-[1.06] tracking-tight text-neutral-950">
            {{ $title }}@if (filled($titleAccent))<span class="text-primary"> {{ $titleAccent }}</span>@endif
        </h1>
        @if (filled($description))
            <p class="mt-4 max-w-2xl text-base font-medium leading-relaxed text-neutral-600 sm:text-[17px]">
                {{ $description }}
            </p>
        @endif
        @isset($slot)
            @if (trim((string) $slot) !== '')
                <div class="mt-6">{{ $slot }}</div>
            @endif
        @endisset
    </div>
</section>
