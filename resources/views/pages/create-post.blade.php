@extends('layouts.app')

@section('title', __('Kent sorunu bildir').' • '.config('app.name'))

@section('content')
    @include('partials.complaint-wizard')
@endsection

@push('scripts')
    <script>
        window.__complaintMaxImages = {{ (int) config('complaint.max_images', 5) }};
        window.__complaintMaxVideos = {{ (int) config('complaint.max_videos', 2) }};
        window.__complaintImageMaxKb = {{ (int) config('complaint.image_max_kb', 6144) }};
        window.__complaintVideoMaxKb = {{ (int) config('complaint.video_max_kb', 35840) }};
    </script>
@endpush
