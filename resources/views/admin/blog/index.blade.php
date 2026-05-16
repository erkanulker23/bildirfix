@extends('layouts.admin')

@section('admin_heading', __('Blog'))
@section('title', __('Blog yazıları'))

@section('content')
    <section class="mx-auto max-w-6xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Blog yazıları') }}</h1>
            <a href="{{ route('admin.blog.create') }}"
                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Yeni yazı') }}</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">{{ __('Başlık') }}</th>
                        <th class="px-4 py-3">{{ __('Slug') }}</th>
                        <th class="px-4 py-3">{{ __('Moderasyon') }}</th>
                        <th class="px-4 py-3">{{ __('Taslak / yayın') }}</th>
                        <th class="px-4 py-3">{{ __('Yayın zamanı') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('İşlem') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($posts as $post)
                        <tr class="text-slate-700">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $post->title }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $post->slug }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800">{{ $post->moderation_status->label() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($post->is_published && $post->published_at)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-900">{{ __('Yayın tarihi') }}</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ __('Taslak') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $post->published_at?->translatedFormat('d.m.Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.blog.edit', $post) }}"
                                    class="font-bold text-blue-600 hover:underline">{{ __('Düzenle') }}</a>
                                @if ($post->isVisibleOnPublicSite())
                                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" target="_blank" rel="noopener"
                                        class="ml-3 font-semibold text-slate-500 hover:text-slate-800">{{ __('Görüntüle') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>{{ $posts->links() }}</div>
    </section>
@endsection
