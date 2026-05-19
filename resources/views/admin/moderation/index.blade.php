@extends('layouts.admin')

@section('admin_heading', __('Şikâyet moderasyonu'))
@section('title', __('Şikâyet moderasyonu'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ __('Kent bildirimleri — moderasyon') }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ __('Ön yüzde yalnızca yayınlanan kayıtlar herkese açıktır. Durum seçerek onaylı, red veya yayından kaldırılanları listeleyebilirsiniz.') }}</p>
            </div>
        </div>

        @include('partials.admin.moderation-filters', ['statusFilter' => $statusFilter, 'routeName' => 'admin.moderation.index'])

        @if ($posts->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500 shadow-sm">
                {{ __('Bu süzgeçte kayıt yok.') }}
            </section>
        @else
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <ul class="divide-y divide-slate-100">
                    @foreach ($posts as $post)
                        <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                @php
                                    $st = $post->moderation_status;
                                    $pill = match ($st) {
                                        \App\Enums\PostModerationStatus::Pending => 'text-amber-600',
                                        \App\Enums\PostModerationStatus::Approved => 'text-emerald-600',
                                        \App\Enums\PostModerationStatus::Rejected => 'text-rose-600',
                                        \App\Enums\PostModerationStatus::Unpublished => 'text-slate-600',
                                    };
                                @endphp
                                <p class="text-[10px] font-bold uppercase tracking-wider {{ $pill }}">{{ $st->label() }}</p>
                                <p class="mt-1 truncate text-base font-bold text-slate-900">{{ $post->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $post->user?->name }} · {{ $post->city?->name }}</p>
                                @if ($post->moderated_at)
                                    <p class="mt-1 text-[11px] text-slate-400">{{ __('Son işlem') }}: {{ $post->moderated_at->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                                        @if ($post->moderatedBy)
                                            · {{ $post->moderatedBy->name }}
                                        @endif
                                    </p>
                                @endif
                                @if ($post->description)
                                    <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $post->description }}</p>
                                @endif
                                @if ($post->moderation_note)
                                    <p class="mt-2 text-xs font-semibold text-slate-600">{{ __('Not') }}: {{ $post->moderation_note }}</p>
                                @endif
                                <p class="mt-2">
                                    <a href="{{ route('posts.show', $post) }}" target="_blank" rel="noopener"
                                        class="text-xs font-bold text-blue-600 hover:underline">{{ __('Ön yüzde aç') }}</a>
                                </p>
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2 sm:flex-col">
                                @if ($st === \App\Enums\PostModerationStatus::Pending)
                                    <form method="POST" action="{{ route('admin.moderation.approve', $post) }}">
                                        @csrf
                                        <input type="hidden" name="durum" value="{{ $statusFilter }}">
                                        <button type="submit"
                                            class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">{{ __('Yayına al') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.moderation.reject', $post) }}" class="space-y-1">
                                        @csrf
                                        <input type="hidden" name="durum" value="{{ $statusFilter }}">
                                        <input type="text" name="moderation_note" maxlength="2000"
                                            placeholder="{{ __('Red gerekçesi (isteğe bağlı)') }}"
                                            class="mb-1 w-full min-w-[12rem] rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400">
                                        <button type="submit"
                                            class="w-full rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-rose-500">{{ __('Reddet') }}</button>
                                    </form>
                                @elseif ($st === \App\Enums\PostModerationStatus::Approved)
                                    <form method="POST" action="{{ route('admin.moderation.unpublish', $post) }}" class="space-y-1">
                                        @csrf
                                        <input type="hidden" name="durum" value="{{ $statusFilter }}">
                                        <input type="text" name="moderation_note" maxlength="2000"
                                            placeholder="{{ __('Yayından kaldırma notu (isteğe bağlı)') }}"
                                            class="mb-1 w-full min-w-[12rem] rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400">
                                        <button type="submit"
                                            class="w-full rounded-xl bg-slate-800 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-slate-700">{{ __('Yayından kaldır') }}</button>
                                    </form>
                                @elseif (in_array($st, [\App\Enums\PostModerationStatus::Rejected, \App\Enums\PostModerationStatus::Unpublished], true))
                                    <form method="POST" action="{{ route('admin.moderation.approve', $post) }}">
                                        @csrf
                                        <input type="hidden" name="durum" value="{{ $statusFilter }}">
                                        <button type="submit"
                                            class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">{{ __('Tekrar yayına al') }}</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 px-4 py-3">{{ $posts->links() }}</div>
            </div>
        @endif
    </div>
@endsection
