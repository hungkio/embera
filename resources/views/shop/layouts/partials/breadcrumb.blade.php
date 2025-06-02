<section class="breadcrumb-area d-flex align-items-center" style="background-image:url({{asset('frontend/img/testimonial/test-bg.jpg')}});">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 offset-xl-3 col-lg-8 offset-lg-2">
                <div class="breadcrumb-wrap text-center">
                    <div class="breadcrumb-title mb-30">
                        <h2>{{ $name ?? null }}</h2>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Trang chủ') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $name ?? null }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
