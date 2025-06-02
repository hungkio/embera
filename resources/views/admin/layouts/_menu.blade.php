<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="fal fa-arrow-left"></i>
        </a>
        {{ __('Menu Chính') }}
        <a href="#" class="sidebar-mobile-expand">
            <i class="fal fa-expand"></i>
            <i class="fal fa-compress"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->


    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user-material">

            <div class="collapse" id="user-nav">
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="icon-comment-discussion"></i>
                            <span>{{ __('Thông báo') }}</span>
                            <span class="badge bg-success-400 badge-pill align-self-center ml-auto">3</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.account-settings.edit') }}" class="nav-link">
                            <i class="fal fa-user-cog"></i>
                            <span>{{ __('Thiết lập tài khoản') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" onclick="$('#logout-form').submit()">
                            <i class="fal fa-sign-out"></i>
                            <span>{{ __('Đăng xuất') }}</span>
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item-header">
                    <div class="text-uppercase font-size-xs line-height-xs">
                        {{ __('Menu') }}
                        <a href="{{ route('admin.dashboard') }}" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block menu-nav">
                            <i class="fal fa-bars"></i>
                        </a>
                    </div>
                    <i class="fal fa-bars navbar-nav-link sidebar-control sidebar-main-toggle" title="{{ __('Menu') }}"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="fal fa-home"></i>
                        <span>
                            {{ __('Trang chủ') }}
                        </span>
                    </a>
                </li>
{{--                @can('taxonomies.view')--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('admin.taxonomies.index') }}"--}}
{{--                           class="nav-link {{ request()->routeIs('admin.taxonomies*') ? 'active' : null }}">--}}
{{--                            <i class="fal fa-folder-tree"></i>--}}
{{--                            <span>{{ __('Danh mục') }}</span></a>--}}
{{--                    </li>--}}
{{--                @endcan--}}


{{--                @can('posts.view')--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('admin.posts.index') }}"--}}
{{--                           class="nav-link {{ request()->routeIs('admin.posts*') ? 'active' : null }}">--}}
{{--                            <i class="fal fa-edit"></i>--}}
{{--                            <span>--}}
{{--                            {{ __("Bài viết") }}--}}
{{--                        </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endcan--}}
{{--                @can('comments.view')--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('admin.comments') }}"--}}
{{--                           class="nav-link {{ request()->routeIs('admin.comments*') ? 'active' : null }}">--}}
{{--                            <i class="fal fa-comment"></i>--}}
{{--                            <span>{{ __('Bình luận') }}</span></a>--}}
{{--                    </li>--}}
{{--                @endcan--}}

{{--                @can('pages.view')--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{ route('admin.pages.index') }}"--}}
{{--                           class="nav-link @if(request()->routeIs('admin.pages*'))active @endif">--}}
{{--                            <i class="fal fa-file"></i>--}}
{{--                            <span>--}}
{{--                             {{ __("Trang") }}--}}
{{--                    </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                @endcan--}}



{{--                @can('banners.view')--}}
{{--                    @if(setting('store_banner', \App\Domain\Banner\Models\Banner::SHOW) == \App\Domain\Banner\Models\Banner::SHOW)--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('admin.banners.index') }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.banners*'))active @endif">--}}
{{--                                <i class="fal fa-image"></i>--}}
{{--                                <span> {{ __("Quảng Cáo") }} </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
{{--                @endcan--}}
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}"
                       class="nav-link @if(request()->routeIs('admin.orders*'))active @endif">
                        <i class="fal fa-image"></i>
                        <span> {{ __("Đơn hàng") }} </span>
                    </a>
                </li>
{{--                @canany(['contacts.view', 'log-search.view', 'subscribe-email.view', 'mail-settings.view'])--}}
{{--                <li class="nav-item-header">--}}
{{--                    <div class="text-uppercase font-size-xs line-height-xs">{{ __('Khách Hàng') }}</div>--}}
{{--                    <i class="fal fa-horizontal-rule" title="{{ __('Khách Hàng') }}"></i></li>--}}
{{--                @endcan--}}
{{--                <!-- Customer -->--}}
{{--                @can('contacts.view')--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('admin.contacts.index') }}"--}}
{{--                       class="nav-link @if(request()->routeIs('admin.contacts.index'))active @endif">--}}
{{--                        <i class="fal fa-phone"></i>--}}
{{--                        <span>--}}
{{--                         {{ __("Liên hệ") }}--}}
{{--                    </span>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                @endcan--}}
{{--                @canany(['subscribe-email.view', 'mail-settings.view'])--}}
{{--                <li class="nav-item nav-item-submenu--}}
{{--{{ request()->routeIs('admin.mail-settings*') || request()->routeIs('admin.contacts.subscribe_email') || request()->routeIs('admin.subs_group*') ? 'nav-item-expanded nav-item-open' : null }}">--}}
{{--                    <a href="#" class="nav-link"><i class="fal fa-mail-bulk"></i> <span>{{ __('Email Marketing') }}</span></a>--}}
{{--                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('Email Marketing') }}">--}}
{{--                        @can('subscribe-email.view')--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('admin.contacts.subscribe_email') }}" class="nav-link @if(request()->routeIs('admin.contacts.subscribe_email'))active @endif">--}}
{{--                                <span>--}}
{{--                                     {{ __("Email list") }}--}}
{{--                                </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        @endcan--}}
{{--                        @can('mail-settings.view')--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{ route('admin.subs_group.index') }}"--}}
{{--                                       class="nav-link @if(request()->routeIs('admin.subs_group*'))active @endif">--}}
{{--                                <span>--}}
{{--                                    {{ __('Nhóm email') }}--}}
{{--                                </span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}
{{--                        @can('mail-settings.view')--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('admin.mail-settings.index', ['tab' => 'mail-template']) }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.mail-settings*'))active @endif">--}}
{{--                                <span>--}}
{{--                                    {{ __('Chiến dịch') }}--}}
{{--                                </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        @endcan--}}

{{--                    </ul>--}}
{{--                </li>--}}
{{--                @endcan--}}

                <!-- System -->
                @canany(['admins.view', 'menus.index', 'log-activities.index', 'admins.view', 'roles.view'])
                <li class="nav-item-header">
                    <div class="text-uppercase font-size-xs line-height-xs">{{ __('Hệ thống') }}</div>
                    <i class="fal fa-horizontal-rule" title="{{ __('Hệ thống') }}"></i></li>
                @endcan
                @canany(['admins.view', 'roles.view'])
                <li class="nav-item nav-item-submenu {{ request()->routeIs('admin.admins*') || request()->routeIs('admin.roles*') ? 'nav-item-expanded nav-item-open' : null }}">
                    <a href="#" class="nav-link"><i class="fal fa-user"></i> <span>{{ __('Tài khoản') }}</span></a>
                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('Tài khoản') }}">
                        @can('admins.view')
                            <li class="nav-item"><a href="{{ route('admin.admins.index') }}"
                                                    class="nav-link @if(request()->routeIs('admin.admins*'))active @endif">{{ __('Tài khoản') }}</a>
                            </li>
                        @endcan
                        @can('roles.view')
                            <li class="nav-item"><a href="{{ route('admin.roles.index') }}"
                                                    class="nav-link @if(request()->routeIs('admin.roles*'))active @endif">{{ __('Vai trò') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcan
{{--                @canany(['admins.view', 'menus.index', 'log-activities.index'])--}}
{{--                <li class="nav-item nav-item-submenu {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.menus*') || request()->routeIs('admin.log-activities*') ? 'nav-item-expanded nav-item-open' : null }}">--}}
{{--                    <a href="#" class="nav-link"><i class="fal fa-solar-system"></i> <span>{{ __('Hệ Thống') }}</span></a>--}}
{{--                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('Hệ Thống') }}">--}}
{{--                    @can('admins.view')--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('admin.settings.edit') }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.settings*'))active @endif">--}}
{{--                                <span>--}}
{{--                                {{ __('Cài đặt chung') }}--}}
{{--                            </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}

{{--                    @can('menus.index')--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="{{ route('admin.menus.index') }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.menus*'))active @endif">--}}
{{--                                <span>--}}
{{--                            {{ __("Quản lý menu") }}--}}
{{--                        </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        --}}{{--<li class="nav-item">--}}
{{--                            <a href="{{ route('admin.internal-links') }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.internal-links*'))active @endif">--}}
{{--                                <span>--}}
{{--                            {{ __("Quản lý link nội tuyến") }}--}}
{{--                        </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}

{{--                    @can('log-activities.index')--}}
{{--                        --}}{{--<li class="nav-item">--}}
{{--                            <a href="{{ route('admin.log-activities.index') }}"--}}
{{--                               class="nav-link @if(request()->routeIs('admin.log-activities*'))active @endif">--}}
{{--                                <span>--}}
{{--                            {{ __("Lịch sử thao tác") }}--}}
{{--                        </span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}
{{--                    </ul>--}}
{{--                </li>--}}
{{--                @endcan--}}
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
