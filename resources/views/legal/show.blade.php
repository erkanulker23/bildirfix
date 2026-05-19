@extends('layouts.app')

@section('title', $pageLabel.' • '.config('app.name'))

@section('content')
    <article class="prose prose-slate prose-sm mx-auto max-w-prose prose-headings:font-black prose-p:leading-relaxed prose-a:text-primary">
        {!! $htmlContent !!}
    </article>
@endsection
