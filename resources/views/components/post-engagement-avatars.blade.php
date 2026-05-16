@props([
    'users',
    'heading',
    'accent' => 'teal',
])

@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $users */
    $ringClass = $accent === 'indigo' ? 'ring-indigo-200/90 bg-indigo-50/80' : 'ring-teal-200/90 bg-teal-50/80';
    $avatarRing = $accent === 'indigo' ? 'ring-indigo-200' : 'ring-teal-200';
    $avatarBg = $accent === 'indigo'
        ? 'bg-gradient-to-br from-indigo-600 to-violet-700'
        : 'bg-gradient-to-br from-teal-500 to-emerald-700';
@endphp

@if ($users->isNotEmpty())
    <section class="rounded-[1.35rem] border border-neutral-200/90 bg-white p-5 shadow-md shadow-neutral-900/[0.04] ring-1 ring-neutral-100 sm:p-6"
        aria-label="{{ $heading }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-[11px] font-black uppercase tracking-[0.18em] text-neutral-500">{{ $heading }}</h3>
            <span class="rounded-full bg-neutral-100 px-3 py-1 text-[11px] font-black tabular-nums text-neutral-700">{{ $users->count() }}</span>
        </div>
        <div class="post-engage-track mt-4 flex flex-wrap gap-2">
            @foreach ($users as $user)
                @php
                    $label = trim((string) ($user->name ?? '?'));
                    preg_match_all('/\p{L}/u', $label, $ch);
                    $ini = (($ch[0] ?? []) !== [])
                        ? mb_strtoupper(implode('', array_slice($ch[0], 0, 2)))
                        : mb_strtoupper(mb_substr($label, 0, 2));
                    $delay = min($loop->index * 48, 1400);
                @endphp
                <span class="inline-flex max-w-full items-center gap-2 rounded-full px-2 py-1.5 ring-1 {{ $ringClass }}"
                    style="animation-delay: {{ $delay }}ms">
                    <span
                        class="{{ $avatarBg }} flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[11px] font-black text-white ring-2 ring-white {{ $avatarRing }}"
                        aria-hidden="true">{{ mb_substr($ini ?: '?', 0, 2) }}</span>
                    <span class="truncate text-[13px] font-semibold text-neutral-900">{{ $label }}</span>
                </span>
            @endforeach
        </div>
    </section>
@endif
