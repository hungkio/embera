<!-- HEADER -->
<header class="header-area">
    <div class="header-top second-header d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 d-none d-lg-block">
                </div>
                <div class="col-lg-3 col-md-8 d-none  d-md-block">
                    <div class="header-cta">
                        <ul>
                            <li>
                                <i class="icon dripicons-mail"></i>
                                <span>{{ setting('store_email') }}</span>
                            </li>
                            <li>
                                <i class="icon dripicons-phone"></i>
                                <span>{{ setting('store_phone') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-3 d-none d-lg-block">
                    <form class="input-group float-left w-75" method="get" action="{{ route('search') }}">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('Tìm kiếm bài viết, trang...') }}"
                               aria-label="{{ __('Tìm kiếm bài viết, trang...') }}" aria-describedby="basic-addon2">
                        <div class="input-group-append" style="height: calc(1.5em + 0.75rem + 0.2rem);">
                            <button class="btn btn-outline-secondary" type="submit" style="line-height: 25%;">{{ __('Tìm kiếm') }}
                            </button>
                        </div>
                    </form>
                    <div class="header-social text-right">
                        <span>
                            <a href="{{ setting('link_facebook', '') }}" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
                            <a href="{{ setting('link_youtube', '') }}" target="_blank" title="Youtube"><i class="fab fa-youtube"></i></a>
                       </span>
                        <!--  /social media icon redux -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="header-sticky" class="menu-area">
        <div class="container">
            <div class="second-menu">
                <div class="row align-items-center">
                    <div class="col-xl-2 col-lg-2">
                        <div class="logo">
                            @if(setting('store_logo'))
                                    <a href="{{ route('home') }}"><img
                                    src="{{ \Storage::url(setting('store_logo')) }}"
                                    alt="logo"></a>
                            @else
                                <a href="{{ route('home') }}" title="{{ setting('store_name') }}">
                                    <span>{{ mb_strimwidth(setting('store_name'), 0, 15) }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8">
                        <div class="main-menu text-right pr-15">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="has-sub">
                                        <a href="{{ route('home') }}">{{ __('Trang chủ') }}</a>
                                    </li>
                                    @if($menuHeaders->isNotEmpty())
                                        @foreach($menuHeaders as $menu)
                                            <li class="{{ $menu->childs->count() > 0 ? 'has-sub' : '' }}">
                                                <a href="{{ $menu->urlMenu() }}"
                                                   class="@if(request()->fullUrl() == $menu->urlMenu())
                                                       active @endif">{{ $menu->name }}</a>
                                                <ul>
                                                    @if($menu->childs->count() > 0)
                                                        @foreach($menu->childs as $child)
                                                            <li>
                                                                <a href="{{ $child->urlMenu() }}"
                                                                   class="@if(request()->fullUrl() == ($child->urlMenu()))
                                                                       active @endif">{{ $child->name }}</a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 d-none d-lg-block">
                        <a href="{{ route('page.contact') }}" class="top-btn">{{ __('Liên hệ') }} <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                    <div class="col-12">
                        <div class="mobile-menu"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header-end -->
