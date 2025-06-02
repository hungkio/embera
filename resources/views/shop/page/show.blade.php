@extends('shop.layouts.app')
@section('title')
    {{ $page->title }} @if(!empty(setting('store_name')))
        -
    @endif
    {{ setting('store_name') }}
    @if(!empty(setting('store_slogan')))
        -
    @endif
    {{ setting('store_slogan') }}
@endsection
@section('seo')
    <link rel="canonical" href="{{ request()->fullUrl() }}">
    <meta name="title" content="{{ $page->title }}">
    <meta name="description" content="{{ $page->meta_description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ $page->meta_title }}">
    <meta property="og:description" content="{{ $page->meta_description }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ setting('store_logo') ? \Storage::url(setting('store_logo')) : '' }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@section('content')
    <!-- main-area -->
    <main>
        <!-- breadcrumb-area -->
        @include('shop.layouts.partials.breadcrumb', [
             'name' => $page->title
        ])
        <!-- breadcrumb-area-end -->
        <div class="container">
            <div class="mt-5 mb-3">
                {!! $page->body !!}
            </div>
        </div>
    </main>
    <!-- main-area-end -->
@endsection
@push('scripts')
    {!! $pageSchemaMarkup->toScript() !!}
@endpush
