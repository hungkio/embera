<!-- Footer -->
<footer class="footer-bg footer-p">
    <div class="overly"><img src="{{ asset('frontend/img/an-bg/footer-bg.png') }}" alt="rest"></div>
    <div class="footer-top pb-30" style="background-color: #ECF1FA;">
        <div class="container">
            <div class="row justify-content-between">

                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="flog mb-35">
                            @if(setting('store_logo'))
                                <a href="{{ route('home') }}"><img
                                        src="{{ \Storage::url(setting('store_logo')) }}"
                                        alt="logo"></a>
                            @else
                                <a href="{{ route('home') }}">
                                    <span>{{ setting('store_name') }}</span>
                                </a>
                            @endif
                        </div>
                        <div class="footer-text mb-20">
                            <p>{!! setting('store_description', __('LAPO chuyên cung cấp các dịch vụ website như thiết kế website chuyên nghiệp, dịch vụ bán website giá rẻ, dịch vụ thuê website, thiết kế logo, bộ nhận diện thương hiệu, dịch vụ quản trị website. Hỗ trợ chủ doanh nghiệp, chủ cửa hàng, cá nhân mở rộng phát triển kinh doanh trên internet')) !!}</p>
                        </div>
                        <div class="footer-social">
                            <a href="{{ setting('link_facebook') }}" target="_blank" title="Facebook"><i
                                    class="fab fa-facebook"></i></a>
                            <a href="{{ setting('link_youtube') }}" target="_blank" title="Youtube"><i
                                    class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>


                <div class="col-xl-2 col-lg-2 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h5>{{ __('Liên kết') }}</h5>
                        </div>
                        <div class="footer-link">
                            <ul>
                                @if($menuFooter1->isNotEmpty())
                                    @foreach($menuFooter1 as $menu)
                                        <li>
                                            <a href="{{ $menu->urlMenu() }}"><i
                                                    class="fas fa-chevron-right"></i> {{ $menu->name }}</a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h5>{{ __('Danh mục') }}</h5>
                        </div>
                        <div class="footer-link">
                            <ul>
                                @if($menuFooter2->isNotEmpty())
                                    @foreach($menuFooter2 as $menu)
                                        <li>
                                            <a href="{{ $menu->urlMenu() }}"><i
                                                    class="fas fa-chevron-right"></i> {{ $menu->name }}</a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="footer-widget mb-30">
                        <div class="f-widget-title">
                            <h5>{{ __('Liện hệ với chúng tôi') }}</h5>
                        </div>
                        <div class="footer-link">
                            <div class="f-contact">
                                <ul>
                                    <li>
                                        <i class="icon dripicons-phone"></i>
                                        <span>{{ setting('store_phone') }}</span>
                                    </li>
                                    <li>
                                        <i class="icon dripicons-mail"></i>
                                        <span><a
                                                href="mailto:{{ setting('store_email') }}">{{ setting('store_email') }}</a></span>
                                    </li>
                                    <li>
                                        <i class="fal fa-map-marker-alt"></i>
                                        <span>{{ setting('store_address') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="copyright-text text-center">
                        <p>{{ __('Bản quyển thuộc') }} &copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ setting('store_name', 'LAPO.VN') }} {{ setting('store_slogan', 'Dịch vụ Website uy tín') }}.</a> {{ __('Được phát triển và duy trì bởi') }} <a
                                href="https://lapo.vn" target="_blank">LAPO.VN</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer-end -->
