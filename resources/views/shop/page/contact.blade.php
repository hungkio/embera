@extends('shop.layouts.app')
@section('title')
    {{ __('Liên hệ với chúng tôi') }}
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
    <meta name="title" content="{{ __('Liên hệ với chúng tôi') }}">
    <meta name="description" content="{{ setting('store_description') }}">
    <meta name="keywords" content="{{ setting('store_name') }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ __('Liên hệ với chúng tôi') }}">
    <meta property="og:title" content="{{ setting('store_name') }} - {{ setting('store_slogan') }}">
    <meta property="og:description" content="{{ setting('store_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ setting('store_logo') ? \Storage::url(setting('store_logo')) : '' }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@section('content')
    <!-- main-area -->
    <main>
        <!-- breadcrumb-area -->
        @include('shop.layouts.partials.breadcrumb', [
            'name' => __('Liên hệ với chúng tôi')
        ])
        <!-- breadcrumb-area-end -->

        <!-- contact-area -->
        <section id="contact" class="contact-area contact-bg pt-100 pb-70 p-relative fix" style="background-image:url(img/an-bg/an-bg11.png); background-size: cover;background-repeat: no-repeat;">
            <div class="container">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="contact-img">
                            <img src="/frontend/img/bg/touch-illustration.png" alt="touch-illustration">
                        </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="section-title mb-60" >
                            <span>{{ __('Liên hệ') }}</span>
                            <h2>{{ __('Liên hệ với chúng tôi') }}</h2>
                        </div>
                    <form action="{{ route('contact.store') }}" id="contact-form" class="contact-form" method="POST">
                        @csrf
                        <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="contact-field p-relative c-name mb-20">
                                    <input type="text" class="form-control" name="first_name" placeholder="{{ __('Họ (*)') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="contact-field p-relative c-name mb-20">
                                    <input type="text" class="form-control" name="last_name" placeholder="{{ __('Tên (*)') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="contact-field p-relative c-email mb-20">
                                    <input type="text" class="form-control" name="email" placeholder="{{ __('Email (*)') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="contact-field p-relative c-phone mb-20">
                                    <input type="text"
                                           onkeyup="this.value=this.value.replace(/[^\d]/,'')"
                                           class="form-control"
                                           name="phone"
                                           placeholder="{{ __('Số điện thoại (*)') }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="contact-field p-relative c-subject mb-20">
                                    <input type="text" class="form-control" name="title" placeholder="{{ __('Tiêu đề (*)') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="contact-field p-relative c-message mb-45">
                                    <textarea name="message" class="form-control" id="message" cols="30" rows="10" placeholder="{{ __('Nội dung (*)') }}" required></textarea>
                                </div>
                            </div>
                            <div class="slider-btn">
                                <button type="submit" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">{{ __('Gửi') }}</button>
                            </div>
                        </div>
                        </div>

                </form>
                    </div>
                </div>

            </div>

        </section>
        <!-- contact-area-end -->
         <!-- brand-area -->
        <section class="brand-area" style="background-image:url(/frontend/img/an-bg/an-bg12.png); background-size: cover;background-repeat: no-repeat;">
            <div class="container">
                <div class="row brand-active">
                    <div class="col-xl-2">
                        <div class="single-brand">
                            <img src="/frontend/img/brand/c-logo.png" alt="img">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="single-brand active">
                              <img src="/frontend/img/brand/c-logo02.png" alt="img">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="single-brand">
                              <img src="/frontend/img/brand/c-logo03.png" alt="img">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="single-brand">
                              <img src="/frontend/img/brand/c-logo04.png" alt="img">
                        </div>
                    </div>
                    <div class="col-xl-2">
                        <div class="single-brand">
                              <img src="/frontend/img/brand/c-logo.png" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- brand-area-end -->
    </main>
    <!-- main-area-end -->
@endsection
