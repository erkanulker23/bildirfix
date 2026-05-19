@extends('layouts.admin')

@section('title', __('Yeni blog yazısı'))

@section('content')
    <div class="max-w-5xl">
        <div class="psc-page-head">
            <h1 class="psc-page-title">{{ __('Yeni blog yazısı') }}</h1>
        </div>
        <form method="POST" action="{{ route('admin.blog.store') }}" class="space-y-6">
            @csrf
            @include('admin.blog._form', ['post' => $post, 'categories' => $categories])
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="psc-btn psc-btn--primary">{{ __('Yayınla / kaydet') }}</button>
                <a href="{{ route('admin.blog.index') }}" class="psc-btn psc-btn--ghost">{{ __('İptal') }}</a>
            </div>
        </form>
    </div>
@endsection
