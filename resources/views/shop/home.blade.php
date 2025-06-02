@extends('shop.layouts.app')
@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
@endpush
@section('title')
    {{ setting('store_name') }}
    @if(!empty(setting('store_slogan')))
        -
    @endif
    {{ setting('store_slogan') }}
@endsection
@section('seo')
    <link rel="canonical" href="{{ request()->fullUrl() }}">
    <meta name="title" content="{{ setting('store_name') }} - {{ setting('store_slogan') }}">
    <meta name="description" content="{{ setting('store_description') }}">
    <meta name="keywords" content="{{ setting('store_name') }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="{{ setting('store_name') }} - {{ setting('store_slogan') }}">
    <meta property="og:description" content="{{ setting('store_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ setting('store_logo') ? \Storage::url(setting('store_logo')) : '' }}">
    <meta property="og:site_name" content="{{ url('') }}">
@stop
@section('content')
<!-- main-area -->
<main>
    <!-- slider-area -->
    <section id="home" class="slider-area fix p-relative">

        <div class="slider-active2">
        <div class="single-slider slider-bg d-flex align-items-center" style="background-image:url(frontend/img/an-bg/header-bg.png)">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="slider-content s-slider-content text-left">
                                <h2 data-animation="fadeInUp" data-delay=".4s">{!! __('Chúng tôi chăm sóc <span>sức khỏe</span> của bạn tốt hơn !') !!}</h2>
                                <p data-animation="fadeInUp" data-delay=".6s">{{ __('Sự khỏe mạnh là nền tảng cơ bản của một cuộc sống vui vẻ, hạnh phúc, là cơ sở quan trọng để mỗi người thực hiện ý tưởng, ước mơ, nguyện vọng của cuộc đời mình. Bởi nếu bệnh tật, ốm đau, chúng ta thường sẽ không còn đủ sức khỏe, tâm trí nào mà lo lắng, suy nghĩ đến những việc khác nữa.') }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <img src="frontend/img/bg/header-img.png" alt="header-img" class="header-img"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- slider-area-end -->
     <!-- services-area -->
    <section id="services" class="services-area services-bg services-two pt-100"  style="background-image:url(frontend/img/an-bg/an-bg02.png); background-size: contain; background-repeat: no-repeat;background-position: center center;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8">
                    <div class="section-title text-center pl-40 pr-40 mb-80" >
                        <span> {{ __('dịch vụ của chúng tôi') }}</span>
                        <h2>{{ __('Dịch vụ đặc biệt chúng tôi dành cho bạn') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row sr-line">
                <div class="col-lg-4 col-md-12">
                    <div class="s-single-services text-center active" >
                        <div class="services-icon">
                            <img src="frontend/img/icon/sr-icon01.png" alt="img">
                        </div>
                        <div class="second-services-content">
                            <h5>{{ __('Khẩn cấp trực tuyến') }}</h5>
                            <p>{{ __('Chúng tôi sẽ phục vụ nhanh nhất có thể để cấp cứu những trường hợp khẩn cấp !') }}</p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                     <div class="s-single-services text-center" >
                        <div class="services-icon">
                           <img src="frontend/img/icon/sr-icon02.png" alt="img">
                        </div>
                        <div class="second-services-content">
                            <h5>{{ __('Cung cấp Thuốc') }}</h5>
                            <p>{{ __('Chúng tôi cấp thuốc uy tín và chất lượng giúp khách hàng khỏe mạnh và an tâm !') }}</p>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="s-single-services text-center" >
                        <div class="services-icon">
                          <img src="frontend/img/icon/sr-icon03.png" alt="img">
                        </div>
                        <div class="second-services-content">
                            <h5>{{ __('Tiêm phòng') }}</h5>
                            <p>{{ __('Dịch vụ này giúp bạn tăng sức đề kháng cho cơ thể  và giúp khỏi các tác nhân gây bệnh bên ngoài !') }}</p>
                        </div>

                    </div>
                </div>


            </div>

        </div>
    </section>
    <!-- services-area-end -->

    <!-- about-area -->
    <section id="about" class="about-area about-p pt-65 pb-80 p-relative" style="background-image:url(frontend/img/an-bg/an-bg03.png); background-size: contain; background-repeat: no-repeat;background-position: center center;">
        <div class="container">
            <div class="row align-items-center">
              <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="s-about-img p-relative">
                        <img src="frontend/img/bg/illlustration.png" alt="img">

                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="about-content s-about-content pl-30">
                        <div class="section-title mb-20">
                            <span>{{ __('Về chúng tôi') }}</span>
                            <h2>{{ __('Chúng tôi chuyên về chẩn đoán y khoa') }}</h2>
                        </div>
                        <p>{{ __('Bằng cách khai thác sức mạnh của công nghệ và đội ngũ nghiên cứu, chúng tôi đang nỗ lực phát triển các chẩn đoán tiên tiến nhằm cải thiện chất lượng, chẩn đoán bệnh và định hướng điều trị với chi phí hợp lý nhất cho bệnh nhân Việt Nam.') }}</p>
                            <ul>
                                <li>
                                    <div class="icon"><i class="fas fa-chevron-right"></i></div>
                                    <div class="text">{{ __('Luôn luôn nâng cấp cải thiện dịch vụ') }}
                                    </div>
                                </li>
                                <li>
                                    <div class="icon"><i class="fas fa-chevron-right"></i></div>
                                    <div class="text">{{ __('Luôn luôn nâng cấp trang thiết bị cao cấp') }}
                                    </div>
                                </li>
                                <li>
                                    <div class="icon"><i class="fas fa-chevron-right"></i></div>
                                    <div class="text">{{ __('Phục vụ khách hàng nhiệt tình & tận tâm') }}
                                    </div>
                                </li>
                            <div></div>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- about-area-end -->

    <!-- counter-area -->
    <div class="counter-area pt-100 pb-100" style="background-image:url(frontend/img/an-bg/an-bg04.png); background-repeat: no-repeat; background-size: contain; ">
        <div class="container">
            <div class="row align-items-end">
                 <div class="col-lg-3 col-md-6 col-sm-12">
                   <div class="single-counter text-center" >
                     <img src="frontend/img/icon/cunt-icon01.png" alt="img">
                        <div class="counter p-relative">
                            <span class="count">{{ __('500') }}</span><small>+</small>
                        </div>
                        <p>{{ __('Bác sĩ') }}</p>
                    </div>
                </div>
              <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="single-counter text-center" >
                        <img src="frontend/img/icon/cunt-icon02.png" alt="img">
                        <div class="counter p-relative">
                            <span class="count">{{ __('58796') }}</span><small>+</small>
                        </div>
                        <p>{{ __('Bệnh nhân khỏi bệnh') }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="single-counter text-center" >
                         <img src="frontend/img/icon/cunt-icon03.png" alt="img">
                        <div class="counter p-relative">
                           <span class="count">{{ __('500') }}</span><small>+</small>
                        </div>
                        <p>{{ __('Giường bệnh') }}</p>
                    </div>
                </div>
                 <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="single-counter text-center" >
                         <img src="frontend/img/icon/cunt-icon04.png" alt="img">
                        <div class="counter p-relative">
                            <span class="count">{{ __('200') }}</span><small>+</small>
                        </div>
                        <p>{{ __('Giải thưởng') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- counter-area-end -->

    <!-- department-area -->
    <section class="department-area cta-bg pb-70 mt-10 fix" style="background-image:url(frontend/img/an-bg/an-bg05.png); background-size: contain;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="section-title mb-50" >
                        <span>{{ __('Phòng khám') }}</span>
                        <h2>{{ __('Chúng tôi chăm sóc sức khỏe của bạn') }}</h2>
                            </div>
                            <ul>
                                <li>
                                    <div class="icon">
                                        <div><img src="frontend/img/icon/de-icon01.png" alt="de-icon"></div></div>
                                    <a href="javascript:" class="text">
                                        <h3>{{ __('Thuốc') }}</h3>
                                        {{ __('Cung cấp nguồn thuốc uy tín và chất lượng.') }}
                                    </a>
                                </li>
                                <li>
                                    <div class="icon">
                                        <div><img src="frontend/img/icon/de-icon02.png" alt="de-icon"></div></div>
                                     <a href="javascript:" class="text">
                                        <h3>{{ __('Dịch vụ') }}</h3>
                                         {{ __('Đem lại trải nghiệm chăm sóc khách hàng tốt nhất.') }}
                                    </a>
                                </li>
                                <li>
                                     <div class="icon">
                                        <div><img src="frontend/img/icon/de-icon03.png" alt="de-icon"></div></div>
                                    <a href="javascript:" class="text">
                                        <h3>{{ __('Khám bệnh') }}</h3>
                                        {{ __('Dụng cụ và chất lượng chuyên môn y, bác sỹ cao và dày dạn kinh nghiệm.') }}
                                    </a>
                                </li>
                        </ul>

                </div>
                <div class="col-lg-6">
                    <div class="s-d-img p-relative">
                        <img src="frontend/img/bg/de-illustration.png" alt="img">

                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- department-area-end -->
     <!-- team-area-->
    <section id="team" class="pb-20" style="background-image:url(frontend/img/an-bg/an-bg13.png); background-size: contain;background-position: center center;background-repeat: no-repeat;">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8">
                    <div class="section-title text-center mb-70">
                         <span> {{ __('Đội ngũ') }} </span>
                        <h2>{{ __('Bác sĩ đứng đầu trong bệnh viện') }}</h2>
                        <p>{{ __('Đội ngũ y, bác sỹ cao và dày dạn kinh nghiệm') }}</p>
                    </div>
                </div>
            </div>
            <div class="row team-active">
                <div class="col-xl-4">
                    <div class="single-team mb-30" >
                        <div class="team-thumb">
                            <div class="brd">
                                 <img src="frontend/img/team/team01.png" alt="img">
                            </div>
                        </div>
                        <div class="team-info">
                            <h4>{{ __('Bác sĩ Hương') }}</h4>
                            <span>{{ __('Giám đốc') }}</span>
                            <p>{{ __('Làm việc từ 2014') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="single-team mb-30" >
                        <div class="team-thumb">
                            <div class="brd">
                                <img src="frontend/img/team/team02.png" alt="img">
                            </div>
                        </div>
                        <div class="team-info">
                            <h4>{{ __('Bác sĩ Nam') }}</h4>
                            <span>{{ __('Trưởng khoa') }}</span>
                            <p>{{ __('Làm việc từ 2015') }}</p>
                        </div>
                    </div>
                </div>
               <div class="col-xl-4">
                    <div class="single-team mb-30" >
                        <div class="team-thumb">
                            <div class="brd">
                                <img src="frontend/img/team/team03.png" alt="img">
                            </div>
                        </div>
                        <div class="team-info">
                            <h4>{{ __('Bác sĩ Thảo') }}</h4>
                            <span>{{ __('Trưởng khoa') }}</span>
                            <p>{{ __('Làm việc từ 2020') }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- team-area-end -->
    <!-- newslater-area -->
    <section class="newslater-area pb-50" style="background-image: url(frontend/img/an-bg/an-bg06.png);background-position: center bottom; background-repeat: no-repeat;" >
        <div class="container">
            <div class="row align-items-end">
                <div class="col-xl-4 col-lg-4 col-lg-4">
                    <div class="section-title mb-100">
                        <span>{{ __('Bản tin') }}</span>
                        <h2>{{ __('Đăng kí nhận bản tin') }}</h2>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4">
                    <form name="ajax-form" id="subscribe-email-form" action="{{ route('contact.subscribe_email') }}" method="post" class="contact-form newslater pb-130">
                        @csrf
                       <div class="form-group">
                          <input class="form-control" id="email2" name="email" type="email" placeholder="Nhập email..." value="" required="">
                          <button type="submit" class="btn btn-custom" id="send2">{{ __('Đăng ký') }} <i class="fas fa-chevron-right"></i></button>
                       </div>
                       <!-- /Form-email -->
                    </form>
                </div>
                <div class="col-xl-4 col-lg-4">
                    <img src="frontend/img/bg/news-illustration.png">
                </div>
            </div>

        </div>
    </section>
    <!-- newslater-aread-end -->
    <!-- testimonial-area -->
    <section id="testimonios" class="testimonial-area testimonial-p pt-50 pb-85 fix" style="background-image: url(frontend/img/an-bg/an-bg07.png);background-position: center; background-repeat: no-repeat;background-size: contain;" >
        <div class="container">
              <div class="row justify-content-center">

                <div class="col-lg-8">
                    <div class="section-title center-align mb-60 text-center">
                        <span>{{ __('Đánh giá khách hàng') }}</span>
                        <h2>{{ __('Khách hàng nói gì về chúng tôi') }}</h2>
                    </div>
                    </div>
                    </div>

           <div class="row justify-content-center">

                <div class="col-lg-10">
                    <div class="testimonial-active">


                        <div class="single-testimonial">
                             <div class="testi-img">
                                <img src="frontend/img/testimonial/testimonial-img.png" alt="img">
                            </div>
                            <div class="single-testimonial-bg">
                            <div class="com-icon"><img src="frontend/img/testimonial/qutation.png" alt="img"></div>
                                <div class="testi-author">
                                             <div class="ta-info">
                                    <h6>{{ __('Bác Sỹ Nam chia sẻ') }}</h6>
                                    <span>{{ __('CEO & Founder') }}</span>

                                </div>
                            </div>
                            <p>{{ __('Đã từng có bệnh nhân đã đi rất nhiều nơi chữa bệnh dạ dày nhưng không khỏi. Đã đến bệnh viện Mecare chúng tôi để chữa trị. Với kinh nghiệm cộng với thiết bị chuyên nghiệp chúng tôi đã chữa khỏi cho bệnh nhân này!') }}</p>
                            </div>

                        </div>

                   </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- testimonial-area-end -->

   <!-- pricing-area -->
    <section id="pricing" class="pricing-area pb-70" style="background-image: url(frontend/img/an-bg/an-bg08.png);background-position: center; background-repeat: no-repeat;" >
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-title center-align mb-60 text-center">
                        <span>{{ __('Bảng giá') }}</span>
                        <h2>{{ __('Gói giá hợp lý') }}</h2>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-lg-4 col-md-12">
                    <div class="pricing-box text-center mb-60" >
                        <div class="pricing-head">
                            <h4>{{ __('Gói tiết kiệm') }}</h4>
                            <div class="price-count mb-30">
                               <h2>{{ __('1.500.000 VNĐ') }}</h2>
                            </div>
                            <img src="frontend/img/icon/pr-icon01.png" alt="pricon">
                        </div>
                        <div class="pricing-body mb-40 text-left">
                            <p>{{ __('Dịch vụ tận tâm') }}</p>
                            <ul>
                                <li>{{ __('Thăm khám') }}</li>
                                <li>{{ __('Điều trị') }}</li>
                                <li>{{ __('Phục hồi') }}</li>
                                <li>{{ __('Chăm sóc') }}</li>
                            </ul>
                        </div>
                        <div class="pricing-btn">
                            <a href="{{ route('page.contact') }}" class="btn">{{ __('Liên hệ') }} <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="pricing-box active text-center mb-60" >
                        <div class="pricing-head">
                            <h4>{{ __('Gói đặt nhiều') }}</h4>
                            <div class="price-count mb-30">
                               <h2>{{ __('2.500.000 VNĐ') }}</h2>
                            </div>
                            <img src="frontend/img/icon/pr-icon02.png" alt="pricon">
                        </div>
                        <div class="pricing-body mb-40 text-left">
                            <p>{{ __('Dịch vụ tận tâm') }}</p>
                            <ul>
                                <li>{{ __('Thăm khám') }}</li>
                                <li>{{ __('Điều trị') }}</li>
                                <li>{{ __('Phục hồi') }}</li>
                                <li>{{ __('Chăm sóc') }}</li>
                                <li>{{ __('Di chuyển') }}</li>
                            </ul>
                        </div>
                        <div class="pricing-btn">
                            <a href="{{ route('page.contact') }}" class="btn">{{ __('Liên hệ') }} <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="pricing-box text-center mb-60" >
                         <div class="pricing-head">
                            <h4>{{ __('Gói VIP') }}</h4>
                            <div class="price-count mb-30">
                               <h2>{{ __('3.500.000') }}</h2>
                            </div>
                            <img src="frontend/img/icon/pr-icon03.png" alt="pricon">
                        </div>
                        <div class="pricing-body mb-40 text-left">
                            <p>{{ __('Dịch vụ tận tâm') }}</p>
                            <ul>
                                <li>{{ __('Thăm khám') }}</li>
                                <li>{{ __('Điều trị') }}</li>
                                <li>{{ __('Phục hồi') }}</li>
                                <li>{{ __('Chăm sóc') }}</li>
                                <li>{{ __('Di chuyển') }}</li>
                                <li>{{ __('Cung cấp thuốc') }}</li>
                            </ul>
                        </div>
                        <div class="pricing-btn">
                        <a href="{{ route('page.contact') }}" class="btn">{{ __('Liên hệ') }} <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- pricing-area-end -->


    <!-- counter-area -->
    <div class="call-area pb-50" style="background-image:url(frontend/img/an-bg/an-bg09.png); background-repeat: no-repeat; background-position: bottom;">
        <div class="container">
            <div class="row align-items-center">
                 <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="single-counter-img fadeInUp animated" >
                         <img src="frontend/img/bg/ap-illustration.png" alt="img" class="img">
                    </div>
                </div>
              <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="section-title mt-100">
                        <span>{{ __('Đặt lịch hẹn') }}</span>
                        <h2>{{ __('Đặt lịch hẹn cho trường hợp khẩn cấp') }}</h2>
                    </div>

                </div>
                <div class="col-lg-2 col-md-12 col-sm-12">
                    <div class="slider-btn mt-130">
                        <a href="{{ route('page.contact') }}" class="btn ss-btn" data-animation="fadeInRight" data-delay=".8s">{{ __('Liên hệ') }} <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- counter-area-end -->

    <!-- blog-area -->
    <section id="blog" class="blog-area  p-relative pt-100 pb-90 fix" style="background-image:url(frontend/img/an-bg/an-bg10.png); background-size: contain;background-repeat: no-repeat;background-position: center center;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="section-title text-center mb-80" >
                      <span> {{ __('Bản tin') }} </span>
                        <h2>{{ __('Cập nhật Thông tin & Tin tức') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @if(count($posts) > 0)
                    @foreach($posts as $post)
                    <div class="col-lg-4 col-md-12">
                        <div class="single-post mb-30" >
                            <div class="blog-thumb">
                                <a href="{{ $post->url() }}">
                                    <img src="{{ $post->getFirstMediaUrl('image') ?? '/admin/global_assets/images/placeholders/placeholder.jpg' }}" alt="img">
                                     <img src="frontend/img/bg/b-link.png" alt="b-link" class="b-link">
                                </a>
                            </div>
                            <div class="blog-content text-center">
                                <div class="b-meta mb-20">
                                   <div class="row">
                                         <div class="col-lg-6 col-md-6">
                                           <i class="far fa-calendar-alt"></i>  {{ $post->created_at->diffForHumans() }}
                                         </div>
                                     </div>
                                </div>
                                <h4><a href="{{ $post->url() }}">{{ $post->title }}</a></h4>
                                 <p>{!! $post->description !!}</p>
                                 <div class="blog-btn"><a href="{{ $post->url() }}">{{ __('Xem thêm') }}</a></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    <!-- blog-area-end -->

    <!-- contact-area -->
    <section id="contact" class="contact-area contact-bg pb-70 p-relative fix" style="background-image:url(frontend/img/an-bg/an-bg11.png); background-size: cover;background-repeat: no-repeat;">
        <div class="container">

            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-img">
                        <img src="frontend/img/bg/touch-illustration.png" alt="touch-illustration">
                    </div>
                </div>
                <div class="col-lg-6">
                <div class="section-title mb-60" >
                        <span>{{ __('Liên hệ') }}</span>
                        <h2>{{ __('Kết nối với chúng tôi') }}</h2>
                    </div>
                <form action="{{ route('contact.store') }}" class="contact-form" id="contact-form" method="POST">
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
                                    <input
                                        type="text"
                                        onkeyup="this.value=this.value.replace(/[^\d]/,'')"
                                        class="form-control"
                                        name="phone"
                                        placeholder="{{ __('Số điện thoại (*)') }}" required>
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
                            <div class="contact-field p-relative c-message mb-45">
                                <textarea name="message" id="message" cols="30" rows="10" placeholder="{{ __('Lời nhắn (*)') }}"></textarea>
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
    <div class="brand-area" style="background-image:url({{ asset('frontend/img/an-bg/an-bg12.png') }}); background-size: cover;background-repeat: no-repeat;">
        <div class="container">
            <div class="row brand-active">
                <div class="col-xl-2">
                    <div class="single-brand">
                        <img src="frontend/img/brand/c-logo.png" alt="img">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="single-brand active">
                          <img src="frontend/img/brand/c-logo02.png" alt="img">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="single-brand">
                          <img src="frontend/img/brand/c-logo03.png" alt="img">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="single-brand">
                          <img src="frontend/img/brand/c-logo04.png" alt="img">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="single-brand">
                          <img src="frontend/img/brand/c-logo.png" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- brand-area-end -->
</main>
<!-- main-area-end -->
@endsection
@push('scripts')
    {!! $homeSchemaMarkup->toScript() !!}
@endpush
