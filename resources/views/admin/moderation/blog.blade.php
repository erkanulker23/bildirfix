@extends('layouts.admin')

@section('admin_heading', __('Blog moderasyonu'))
@section('title', __('Blog moderasyonu'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ __('Onay bekleyen blog yazıları') }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ __('Süper yönetici onayından sonra yazılar sitede yayınlanır.') }}</p>
        </div>

        @if ($posts->isEmpty())
            <section class="rounded-xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-500 shadow-sm">
                {{ __('Bekleyen blog yazısı yok.') }}
            </section>
        @else
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <ul class="divide-y divide-slate-100">
                    @foreach ($posts as $post)
                        <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ __('Bekliyor') }}</p>
                                <p class="mt-1 truncate text-base font-bold text-slate-900">{{ $post->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $post->author?->name }} · {{ $post->slug }}</p>
                                @if ($post->excerpt)
                                    <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $post->excerpt }}</p>
                                @endif
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2 sm:flex-col">
                                <form method="POST" action="{{ route('admin.blog-moderation.approve', $post) }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500">{{ __('Yayına al') }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.blog-moderation.reject', $post) }}" class="space-y-1">
                                    @csrf
                                    <input type="text" name="moderation_note" maxlength="2000"
                                        placeholder="{{ __('Red gerekçesi (isteğe bağlı)') }}"
                                        class="mb-1 w-full min-w-[12rem] rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 placeholder:text-slate-400">
                                    <button type="submit"
                                        class="w-full rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-rose-500">{{ __('Reddet') }}</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 px-4 py-3">{{ $posts->links() }}</div>
            </div>
        @endif
    </div>
@endsection
