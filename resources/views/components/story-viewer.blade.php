<div id="story-viewer" x-data="dsStoryViewer" x-show="visible" x-cloak x-transition.opacity.duration.200ms
    class="fixed inset-0 z-[1000] bg-black" role="dialog" aria-modal="true" aria-label="{{ __('Hikâyeler') }}">

    <div class="contents">
        <div class="absolute left-0 right-0 top-0 z-10 flex gap-1 px-3 pt-3 safe-top">
            <template x-for="(story, index) in stories" :key="story.id">
                <div class="h-[3px] flex-1 overflow-hidden rounded-full bg-white/30">
                    <div class="h-full rounded-full bg-white transition-none"
                        :style="`width: ${index < currentIndex ? 100 : index === currentIndex ? progress : 0}%`"></div>
                </div>
            </template>
        </div>

        <div class="absolute left-0 right-0 top-10 z-10 flex items-start justify-between px-4 py-3 safe-top">
            <div class="flex min-w-0 items-center gap-3">
                <template x-if="currentStory">
                    <span
                        class="font-heading flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white ring-2 ring-white/40"
                        x-text="String(currentStory.user?.name || '?').substring(0, 2).toUpperCase()"></span>
                </template>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold leading-none text-white" x-text="currentStory?.user?.name"></p>
                    <p class="mt-0.5 text-xs text-white/60" x-text="currentStory?.created_at_human"></p>
                </div>
            </div>
            <button type="button" @click="closeViewer()"
                class="flex h-11 w-11 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm transition-colors hover:bg-white/30">
                <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
                <span class="sr-only">{{ __('Kapat') }}</span>
            </button>
        </div>

        <div class="absolute inset-0 touch-pan-y" @click="handleViewerClick($event)" @touchstart.passive="handleTouchStart"
            @touchend.passive="handleTouchEnd">
            <template x-if="currentStory?.media_type === 'video'">
                <video :src="currentStory.media_url" class="h-full w-full object-cover" autoplay muted playsinline
                    @ended="nextStory()"></video>
            </template>
            <template x-if="currentStory && currentStory.media_type !== 'video'">
                <img :src="currentStory.media_url" alt="" class="h-full w-full object-cover">
            </template>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60"></div>
        </div>

        <div class="safe-bottom absolute bottom-0 left-0 right-0 z-10 px-4 pb-8 pt-4">
            <template x-if="currentStory?.location_text">
                <div class="mb-2 flex items-center gap-1.5 text-sm text-white">
                    <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" aria-hidden="true">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                    <span x-text="currentStory.location_text"></span>
                </div>
            </template>
            <p class="mb-4 text-base font-medium text-white" x-text="currentStory?.description"></p>
            <p class="text-xs font-medium text-white/70">{{ __('Sağ / sol veya ekranın üçte birine dokunarak ilerleyin.') }}</p>
        </div>
    </div>
</div>
