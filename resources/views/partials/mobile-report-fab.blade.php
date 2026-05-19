@if (empty($minimalChrome ?? false) && ! request()->routeIs('posts.create', 'complaints.quick.create', 'home'))
    <div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 md:hidden" aria-hidden="true">
        <div class="pointer-events-auto mx-auto max-w-lg px-4 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
            <a href="{{ route('posts.create') }}"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 text-[15px] font-black text-white shadow-[0_8px_28px_-6px_rgba(234,88,12,0.65)] ring-2 ring-white transition active:scale-[0.98] hover:bg-primary-hover">
                <span class="text-lg leading-none" aria-hidden="true">+</span>
                {{ __('Bildir') }}
            </a>
        </div>
    </div>
    <div class="h-[4.5rem] shrink-0 md:hidden" aria-hidden="true"></div>
@endif
