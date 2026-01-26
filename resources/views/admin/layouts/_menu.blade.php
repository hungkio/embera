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
            <ul class="nav nav-sidebar">

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
                        <span>{{ __('Trang chủ') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.map.index') }}"
                         class="nav-link @if(request()->routeIs('admin.map*')) active @endif">
                         <i class="fal fa-map-marked-alt"></i>
                                        <span>{{ __("Bản đồ") }}</span>
                                    </a>
                                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}"
                       class="nav-link @if(request()->routeIs('admin.orders*'))active @endif">
                        <i class="fal fa-image"></i>
                        <span>{{ __("Đơn hàng") }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.merchants.index') }}"
                       class="nav-link @if(request()->routeIs('admin.merchants.index'))active @endif">
                        <i class="fal fa-user-tie"></i>
                        <span>{{ __("Đối tác") }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.contracts.index') }}"
                       class="nav-link @if(request()->routeIs('admin.contracts*'))active @endif">
                        <i class="fal fa-file-contract"></i>
                        <span>{{ __("Hợp Đồng") }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.shops.index') }}"
                       class="nav-link @if(request()->routeIs('admin.shops.index'))active @endif">
                        <i class="fal fa-store"></i>
                        <span>{{ __("Cửa hàng") }}</span>
                    </a>
                </li>
                <!-- Pins (chỉ hiển thị cho người có quyền pins.view) -->
                @can('pins.view')
                <li class="nav-item">
                    <a href="{{ route('admin.pins.index') }}"
                       class="nav-link @if(request()->routeIs('admin.pins*'))active @endif">
                        <i class="fal fa-battery-full"></i>
                        <span>{{ __('Pins') }}</span>
                    </a>
                </li>
                @endcan
            <!-- Devices (chỉ hiển thị cho người có quyền devices.view) -->
{{--                @can('device-status.view')--}}
                <li class="nav-item">
                    <a href="{{ route('admin.device-status.index') }}"
                       class="nav-link @if(request()->routeIs('admin.device-status*'))active @endif">
                        <i class="fal fa-plug"></i>
                        <span>{{ __('Trạng thái thiết bị') }}</span>
                    </a>
                </li>
{{--                @endcan--}}
                <!-- System -->
                @canany(['admins.view', 'menus.index', 'log-activities.index', 'admins.view', 'roles.view'])
                <li class="nav-item-header">
                    <div class="text-uppercase font-size-xs line-height-xs">{{ __('Hệ thống') }}</div>
                    <i class="fal fa-horizontal-rule" title="{{ __('Hệ thống') }}"></i>
                </li>
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
                <li class="nav-item nav-item-submenu {{ request()->routeIs('admin.shops.revenue') ? 'nav-item-expanded nav-item-open' : null }}">
                    <a href="#" class="nav-link"><i class="fal fa-user"></i> <span>{{ __('Báo Cáo') }}</span></a>
                    <ul class="nav nav-group-sub" data-submenu-title="{{ __('Báo Cáo') }}">
                        @can('admins.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.shops.revenue') }}"
                               class="nav-link @if(request()->routeIs('admin.shops.revenue'))active @endif">
                                <i class="fal fa-money-bill"></i>
                                <span>{{ __("Doanh thu Shop") }}</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.mergeTransaction*') ? 'active' : null }}">
                    <a href="{{ route('admin.mergeTransaction') }}"
                       class="nav-link @if(request()->routeIs('admin.mergeTransaction*'))active @endif">
                        <i class="fal fa-money-bill"></i>{{ __('Đối soát MB') }}</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.share-logs.index') }}"
                       class="nav-link @if(request()->routeIs('admin.merchants-history.index')) active @endif">
                        <i class="fal fa-history"></i>
                        <span>{{ __("Lịch sử chia sẻ") }}</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
