@extends('shop.layouts.app')
@section('title')
    {{ $post->title }}
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
    <meta name="title" content="{{ $post->title }}">
    <meta name="description" content="{{ $post->meta_description }}">
    <meta name="keywords" content="{{ $post->meta_keywords }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ $post->meta_title }}">
    <meta property="og:description" content="{{ $post->meta_description }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ $post->getFirstMediaUrl('image') }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@push('styles')
@endpush
@section('content')
    <!-- main-area -->
    <main>
        <!-- breadcrumb-area -->
    @include('shop.layouts.partials.breadcrumb', [
         'name' => $post->title
    ])
    <!-- breadcrumb-area-end -->
        <!-- inner-blog -->
        <section class="inner-blog b-details-p pt-100 pb-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="blog-details-wrap">
                            <div class="bsingle__post-thumb mb-30">
                                <img
                                    src="{{ $post->getFirstMediaUrl('image') ?? '/backend/global_assets/images/placeholders/placeholder.jpg' }}"
                                    alt="">
                            </div>
                            <div class="meta__info">
                                <ul>
                                    <li><a href="#"> <i
                                                class="far fa-calendar-alt"></i> {{ formatDate($post->created_at) }}
                                        </a></li>
                                </ul>
                            </div>
                            <div class="details__content pb-50">
                                <h2>{{ $post->title }}</h2>
                                <p>
                                    {!! $post->body !!}
                                </p>
                                <div class="row">
                                    <div class="col-xl-6 col-md-7">
                                    </div>
                                    <div class="col-xl-6 col-md-5">
                                        <div class="post__share text-right">
                                            <h5>{{ __('Mạng Xã Hội') }}</h5>
                                            <ul>
                                                <li>
                                                    <a href="{{ setting('link_facebook') }}" target="_blank"
                                                       title="Facebook"><i class="fab fa-facebook"></i></a>
                                                </li>
                                                <li>
                                                    <a href="{{ setting('link_youtube') }}"
                                                       target="_blank" title="Youtube"><i
                                                            class="fab fa-youtube"></i></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="related__post mt-45 mb-85">
                                <div class="post-title">
                                    <h4>{{ __('Bài viết liên quan') }}</h4>
                                </div>
                                <div class="row">
                                    @if(count($relatedPosts) > 0)
                                        @foreach($relatedPosts as $relatedPost)
                                            <div class="col-md-6">
                                                <div class="related-post-wrap mb-30">
                                                    <div class="post-thumb">
                                                        <img width="80px"
                                                             src="{{ $relatedPost->getFirstMediaUrl('image') ?? '/backend/global_assets/images/placeholders/placeholder.jpg' }}"
                                                             alt="">
                                                    </div>
                                                    <div class="rp__content">
                                                        <h3>
                                                            <a href="{{ $relatedPost->url() }}">{{ $relatedPost->title }}</a>
                                                        </h3>
                                                        <p>{!! $post->description !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <aside>
                            @if($taxons->isNotEmpty())
                                <div class="widget mb-40">
                                    <div class="widget-title text-center">
                                        <h4>{{ __('Danh mục bài viết') }}</h4>
                                    </div>
                                    <ul class="cat__list">
                                        @foreach($taxons as $taxon)
                                            <li class="{{ count($taxon->childs) > 0 ? 'has-sub' : '' }}">
                                                <a href="{{ $taxon->urlPost() }}">{{ $taxon->name }}</a>
                                                <ul>
                                                    @if(count($taxon->childs) > 0)
                                                        @foreach($taxon->childs as $child)
                                                            <li>
                                                                <a href="{{ $child->urlPost() }}">{{ $child->name }}</a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

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
                                                        <span><i
                                                                class="far fa-user"></i>{{ $post->user->fullname }}</span>
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
@push('scripts')
    {!! $postSchemaMarkup->toScript() !!}
@endpush
