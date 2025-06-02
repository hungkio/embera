@extends('shop.layouts.app')
@section('title')
    {{ $category->name ?? null }}
    @if(!empty(setting('store_name')))
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
    <meta name="title" content="{{ $category->name ?? null }}">
    <meta name="description" content="{{ $category->meta_description ?? null }}">
    <meta name="keywords" content="{{ $category->meta_keywords ?? null }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ $category->meta_title ?? null }}">
    <meta property="og:description" content="{{ $category->meta_description ?? null }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ setting('store_logo') ? \Storage::url(setting('store_logo')) : '' }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@section('content')
    <!-- main-area -->
    <main>
        <!-- breadcrumb-area -->
    @include('shop.layouts.partials.breadcrumb', [
         'name' => $category->name
    ])
    <!-- breadcrumb-area-end -->
        <!-- inner-blog -->
        <section class="inner-blog pt-100 pb-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        @if($posts->count() == 0)
                            <div class="text-center">
                                <p>{{ __('Chưa có bài viết nào !') }}</p>
                            </div>
                        @else
                            @foreach($posts as $post)
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
                                                            class="far fa-calendar-alt"></i> {{ formatDate($post->created_at) }}
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
                                            <a href="{{ $post->url() }}" class="btn ss-btn" data-animation="fadeInRight"
                                               data-delay=".8s">{{ __('Đọc thêm') }} <i
                                                    class="fas fa-chevron-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="pagination-wrap mb-50">
                            {{ $posts->appends('category', request('category'))->links() }}
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <aside>
                            <div class="widget mb-40">
                                <div class="widget-title text-center">
                                    <h4>{{ __('Danh mục bài viết') }}</h4>
                                </div>
                                <ul class="cat__list">
                                    @if($taxons->isNotEmpty())
                                        @foreach($taxons as $taxon)
                                            <li class="{{ $taxon->childs->count() > 0 ? 'has-sub' : '' }}">
                                                <a href="{{ $taxon->urlPost() }}">{{ $taxon->name }}</a>
                                                <ul>
                                                    @if($taxon->childs->count() > 0)
                                                        @foreach($taxon->childs as $child)
                                                            <li>
                                                                <a href="{{ $child->urlPost() }}">{{ $child->name }}</a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            @if(count($postNews) > 0)
                                <div class="widget mb-40">
                                    <div class="widget-title text-center">
                                        <h4>{{ __('Bài viết mới nhất') }}</h4>
                                    </div>
                                    <div class="widget__post">
                                        <ul>
                                            @foreach($postNews as $postNew)
                                                <li>
                                                    <div class="widget__post-thumb">
                                                        <img width="80px"
                                                             src="{{ $postNew->getFirstMediaUrl('image') ?? '/backend/global_assets/images/placeholders/placeholder.jpg' }}"
                                                             alt="">
                                                    </div>
                                                    <div class="widget__post-content">
                                                        <h6><a href="{{ $postNew->url() }}">{{ $postNew->title }}</a>
                                                        </h6>
                                                        <span><i class="far fa-clock"></i>{{ formatDate($postNew->created_at) }}</span>
                                                        <span><i class="far fa-user"></i>{{ $postNew->user->fullname }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="widget mb-40">
                                    <div class="widget-title text-center">
                                        <h4>{{ __('Đăng ký nhận bản tin') }}</h4>
                                    </div>
                                    <form class="widget__post" action="{{ route('contact.subscribe_email') }}" method="post"
                                          id="subscribe-email-form">
                                        @csrf
                                        <div class="contact-field p-relative c-subject mb-20">
                                            <input type="email" name="email"
                                                   placeholder="{{ __('Nhập Email (*)') }}">
                                        </div>
                                        <div class="slider-btn text-center">
                                            <button type="submit" class="btn ss-btn" data-animation="fadeInRight"
                                                    data-delay=".8s">{{ __('Đăng ký') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </aside>
                    </div>
                </div>
            </div>
        </section>
        <!-- inner-blog-end -->
    </main>
    <!-- main-area-end -->
@endsection
