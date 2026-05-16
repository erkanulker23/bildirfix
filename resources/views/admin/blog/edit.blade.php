@extends('layouts.admin')

@section('admin_heading', __('Blog düzenle'))
@section('title', __('Blog düzenle'))

@section('content')
    <section class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <h1 class="text-xl font-extrabold text-slate-900">{{ __('Yazıyı düzenle') }}</h1>
            <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" class="inline"
                onsubmit="return confirm({{ json_encode(__('Bu yazı silinsin mi?')) }});">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-800 hover:bg-rose-100">{{ __('Sil') }}</button>
            </form>
        </div>
        <form method="POST" action="{{ route('admin.blog.update', $post) }}" class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')
            @include('admin.blog._form', ['post' => $post])
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Güncelle') }}</button>
                <a href="{{ route('admin.blog.index') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">{{ __('Listeye dön') }}</a>
                @if ($post->isVisibleOnPublicSite())
                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" target="_blank" rel="noopener"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-3 text-sm font-bold text-emerald-900 hover:bg-emerald-100">{{ __('Yayını aç') }}</a>
                @endif
            </div>
        </form>
    </section>
@endsection
