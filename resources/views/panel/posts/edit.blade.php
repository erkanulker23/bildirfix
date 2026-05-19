@extends('layouts.panel', ['panelKind' => 'user'])

@section('title', __('Bildirim düzenle'))

@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="psc-page-head">
            <div>
                <h1 class="psc-page-title">{{ __('Bildirim düzenle') }}</h1>
                <p class="psc-page-desc">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</p>
            </div>
            <a href="{{ route('panel.posts.index') }}" class="psc-btn psc-btn--ghost">← {{ __('Listeye dön') }}</a>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @include('partials.panel-moderation-badge', ['status' => $post->moderation_status])
            <span class="text-xs text-[#64748b]">{{ $post->status->label() }}</span>
            <span class="text-xs text-[#64748b]">· {{ number_format((int) $post->view_count) }} {{ __('görüntülenme') }}</span>
            @if ($post->isPubliclyApproved())
                <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-[#ea580c] hover:underline">{{ __('Yayında gör') }}</a>
            @endif
        </div>

        @if (! $canEdit)
            <div class="psc-alert psc-alert--warn">{{ __('Yayındaki bildirimler düzenlenemez. Yalnızca görüntüleyebilirsin.') }}</div>
        @endif

        @if ($post->moderation_note)
            <div class="psc-alert psc-alert--error">
                <p class="font-semibold">{{ __('Moderasyon notu') }}</p>
                <p class="mt-1 text-sm">{{ $post->moderation_note }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('panel.posts.update', $post) }}" class="psc-card">
            <div class="psc-card__body space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="psc-field__label" for="post-title">{{ __('Başlık') }}</label>
                    <input id="post-title" name="title" type="text" required maxlength="255"
                        value="{{ old('title', $post->title) }}" class="psc-input mt-2" @disabled(! $canEdit)>
                    @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="psc-field__label" for="post-description">{{ __('Açıklama') }}</label>
                    <textarea id="post-description" name="description" rows="8" required maxlength="8000"
                        class="psc-input mt-2 min-h-[10rem]" @disabled(! $canEdit)>{{ old('description', $post->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                @if ($canEdit)
                    <button type="submit" class="psc-btn psc-btn--primary">{{ __('Kaydet') }}</button>
                @endif
            </div>
        </form>
    </div>
@endsection
