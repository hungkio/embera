@extends('shop.layouts.app')
@section('title')
    {{ __('Tìm kiếm') }} @if(!empty(setting('store_name')))
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
    <meta name="title" content="{{ request('search') }}">
    <meta name="description" content="{{ request('search') }}">
    <meta name="keywords" content="{{ request('search') }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ request('search') }}">
    <meta property="og:description" content="{{ request('search') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ setting('store_logo') ? \Storage::url(setting('store_logo')) : '' }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@section('content')
    <!-- main-area -->
    <main>
        <!-- breadcrumb-area -->
    @include('shop.layouts.partials.breadcrumb', [
         'name' => 'Tìm kiếm'
    ])
    <!-- breadcrumb-area-end -->
        <!-- inner-blog -->
        <section class="inner-blog pt-100 pb-50">
            <div class="container">
                @if($posts->isEmpty() && $pages->isEmpty())
                    <div class="row">

                        <div class="text-center">
                            <p>{{ __('Không tìm thấy kết quả tìm kiếm với từ khoá này!') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <p>{!! __('Tìm thấy :count kết quả tìm kiếm với từ khoá <b>:key</b>!',
['count' => ($posts->count() + $pages->count()), 'key' => request('search') ])  !!} </p>
                    </div>
                    <div class="row">
                        @if(!$posts->isEmpty())
                            @foreach($posts as $post)
                                <div class="col-lg-6">
                                    <div class="bsingle__post mb-50">
                                        <div class="bsingle__post-thumb">
                                            <img
                                                src="{{ $post->getFirstMediaUrl('image') ?? '/backend/global_assets/images/placeholders/placeholder.jpg' }}"
                                                alt="">
                                        </div>
                                        <div class="bsingle__content">
                                            <div class="meta-info">
                                                <ul>
                                                    <li><a href="javascript:void(0)"> <i
                                                                class="far fa-calendar-alt"></i> {{ $post->created_at->diffForHumans() }}
                                                        </a></li>
                                                </ul>
                                            </div>
                                            <h2>
                                                <a href="{{ $post->url() }}">
                                                    {{ $post->title }}
                                                </a>
                                            </h2>
                                            <p>{!! $post->description !!}</p>
                                            <div class="slider-btn">
                                                <a href="{{ $post->url() }}" class="btn ss-btn"
                                                   data-animation="fadeInRight"
                                                   data-delay=".8s">{{ __('Đọc thêm') }} <i
                                                        class="fas fa-chevron-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @if(!$pages->isEmpty())
                            @foreach($pages as $page)
                                <div class="col-lg-6">
                                    <div class="bsingle__post mb-50">
                                        <div class="bsingle__content">
                                            <div class="meta-info">
                                                <ul>
                                                    <li><a href="javascript:void(0)"> <i
                                                                class="far fa-calendar-alt"></i> {{ $page->created_at->diffForHumans() }}
                                                        </a></li>
                                                </ul>
                                            </div>
                                            <h2>
                                                <a href="{{ $page->url() }}">
                                                    {{ $page->title }}
                                                </a>
                                            </h2>
                                            <div class="slider-btn">
                                                <a href="{{ $page->url() }}" class="btn ss-btn"
                                                   data-animation="fadeInRight"
                                                   data-delay=".8s">{{ __('Đọc thêm') }} <i
                                                        class="fas fa-chevron-right"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        </section>
        <!-- inner-blog-end -->
    </main>
    <!-- main-area-end -->
@endsection
@push('scripts')
    <script src="{{ asset('frontend/js/subscribe-email.js') }}"></script>
@endpush
