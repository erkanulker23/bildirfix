@if (empty($minimalChrome ?? false) && ! request()->routeIs('posts.create', 'complaints.quick.create'))
    <a href="{{ route('posts.create') }}"
        class="fab-share fab-share--mobile md:hidden"
        title="{{ __('Kent sorunu bildir') }}"
        aria-label="{{ __('Hızlı bildir') }}">
        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.75"
            stroke-linecap="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14" />
        </svg>
    </a>
@endif
