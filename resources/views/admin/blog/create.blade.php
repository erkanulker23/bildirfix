@extends('layouts.admin')

@section('admin_heading', __('Yeni yazı'))
@section('title', __('Yeni blog yazısı'))

@section('content')
    <section class="mx-auto max-w-3xl space-y-6">
        <h1 class="text-xl font-extrabold text-slate-900">{{ __('Yeni blog yazısı') }}</h1>
        <form method="POST" action="{{ route('admin.blog.store') }}" class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @include('admin.blog._form', ['post' => $post])
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700">{{ __('Kaydet') }}</button>
                <a href="{{ route('admin.blog.index') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">{{ __('İptal') }}</a>
            </div>
        </form>
    </section>
@endsection
