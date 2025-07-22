@extends('admin.layouts.master')
@section('title', __('Trang chủ'))
@section('page-header')
    <x-page-header>
        <x-slot name='title'>
            <h4><i class="icon-cube mr-2"></i> <span class="font-weight-semibold">{{ __('Trang chủ') }}</span></h4>
        </x-slot>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop
@push('css')
    <link rel="stylesheet" href="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.css">
    <style>
        .card-body {
            padding: 1.750rem 1rem;
        }

        .card-body .font-size-theme {
            font-size: 0.7875rem;
        }

        .jvectormap-zoomin {
            display: none;
        }

        .jvectormap-zoomout {
            display: none;
        }

        .has-bg-image {
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 20px;
            border-radius: 10px;
        }

        .card-box-analytics {
            box-shadow: 0px 0px 1px 1px #0c213a1a;
            border-radius: 10px;
        }
    </style>
@endpush

@push('js')
    <script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-world-mill.js"></script>
    <script src="/backend/global_assets/js/vendors/echarts/echarts.min.js"></script>
    {{--    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>--}}
    <script !src="">
        $(function () {
            var merchantChart = echarts.init(document.getElementById('merchantChart'));

            const data = [["2000-06-05", 116], ["2000-06-06", 129], ["2000-06-07", 135], ["2000-06-08", 86], ["2000-06-09", 73], ["2000-06-10", 85], ["2000-06-11", 73], ["2000-06-12", 68], ["2000-06-13", 92], ["2000-06-14", 130], ["2000-06-15", 245], ["2000-06-16", 139], ["2000-06-17", 115], ["2000-06-18", 111], ["2000-06-19", 309], ["2000-06-20", 206], ["2000-06-21", 137], ["2000-06-22", 128], ["2000-06-23", 85], ["2000-06-24", 94], ["2000-06-25", 71], ["2000-06-26", 106], ["2000-06-27", 84], ["2000-06-28", 93], ["2000-06-29", 85], ["2000-06-30", 73], ["2000-07-01", 83], ["2000-07-02", 125], ["2000-07-03", 107], ["2000-07-04", 82], ["2000-07-05", 44], ["2000-07-06", 72], ["2000-07-07", 106], ["2000-07-08", 107], ["2000-07-09", 66], ["2000-07-10", 91], ["2000-07-11", 92], ["2000-07-12", 113], ["2000-07-13", 107], ["2000-07-14", 131], ["2000-07-15", 111], ["2000-07-16", 64], ["2000-07-17", 69], ["2000-07-18", 88], ["2000-07-19", 77], ["2000-07-20", 83], ["2000-07-21", 111], ["2000-07-22", 57], ["2000-07-23", 55], ["2000-07-24", 60]];
            const dateList = data.map(function (item) {
                return item[0];
            });
            const valueList = data.map(function (item) {
                return item[1];
            });
            var merchantOption = {
                title: {
                    text: 'Số lượng Merchant (tháng)',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: {
                    type: 'category',
                    data: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
                    boundaryGap: false
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Doanh số',
                    type: 'line',
                    smooth: true,
                    data: [10, 22, 28, 43, 35, 50],
                    lineStyle: {
                        width: 3,
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            {offset: 0, color: '#6dd5ed'},
                            {offset: 1, color: '#2193b0'}
                        ])
                    },
                    areaStyle: {
                        opacity: 0.4,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#6dd5ed'},
                            {offset: 1, color: '#ffffff'}
                        ])
                    },
                    symbol: 'circle',
                    symbolSize: 8
                }]
            };

            // Gán cấu hình vào biểu đồ
            merchantChart.setOption(merchantOption);

            var userChart = echarts.init(document.getElementById('userChart'));

            var userOption = {
                title: {
                    text: 'Tăng trưởng người dùng',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '5%',
                    right: '5%',
                    bottom: '5%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: ['Sản phẩm A', 'Sản phẩm B', 'Sản phẩm C', 'Sản phẩm D']
                },
                series: [{
                    name: 'Doanh số',
                    type: 'bar',
                    data: [120, 200, 150, 80],
                    barWidth: '40%',
                    itemStyle: {
                        borderRadius: 6,
                        color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                            {offset: 0, color: '#f7971e'},
                            {offset: 1, color: '#ffd200'}
                        ])
                    },
                    label: {
                        show: true,
                        position: 'right',
                        fontWeight: 'bold'
                    }
                }]
            };

            userChart.setOption(userOption);

            var shopTypeChart = echarts.init(document.getElementById('shopTypeChart'));

            var shopType = {
                title: {
                    text: 'Top 5 shop type',
                    left: 'center'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                grid: {
                    left: '5%',
                    right: '5%',
                    bottom: '10%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6'],
                    axisTick: {
                        alignWithLabel: true
                    }
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    name: 'Số lượng',
                    type: 'bar',
                    data: [35, 70, 95, 60, 80],
                    barWidth: '50%',
                    itemStyle: {
                        borderRadius: [6, 6, 0, 0],
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#00c6ff'},
                            {offset: 1, color: '#0072ff'}
                        ])
                    },
                    label: {
                        show: true,
                        position: 'top',
                        fontWeight: 'bold'
                    }
                }]
            };

            shopTypeChart.setOption(shopType);
        });
    </script>
@endpush

@section('page-content')
    <div class="row">
        <div class="col" id="merchantChart" style="width: 600px; height: 400px;"></div>
        <div class="col" id="userChart" style="width: 600px; height: 400px;"></div>
        <div class="col" id="shopTypeChart" style="width: 600px; height: 400px;"></div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #0052D4;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #6FB1FC, #4364F7, #0052D4);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #6FB1FC, #4364F7, #0052D4); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.taxonomies.index') }}"
                                            class="text-white">{{ formatNumber($totalTaxonomy) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.taxonomies.index') }}"
                                                                        class="text-white">{{ __('Loại danh mục') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.taxonomies.index') }}">
                            <i class="fal fa-2x fa-file-alt text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #2193b0;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #6dd5ed, #2193b0);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #6dd5ed, #2193b0); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.pages.index') }}"
                                            class="text-white">{{ formatNumber($totalPages) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.pages.index') }}"
                                                                        class="text-white">{{ __('Trang') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.pages.create') }}">
                            <i class="fal fa-2x fa-file-alt text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #FF512F;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #F09819, #FF512F);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #F09819, #FF512F); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.posts.index') }}"
                                            class="text-white">{{ formatNumber($totalPosts) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.posts.index') }}"
                                                                        class="text-white">{{ __('Bài viết') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.posts.create') }}">
                            <i class="fal fa-2x fa-edit text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (setting('store_banner', \App\Domain\Banner\Models\Banner::SHOW) == \App\Domain\Banner\Models\Banner::SHOW)
            <div class="col-sm-6 col-xl-3">
                <div class="card card-body has-bg-image" style="background: #36d1dc;
                background: -webkit-linear-gradient(to right, #36d1dc, #5b86e5);
                background: linear-gradient(to right, #36d1dc, #5b86e5);">
                    <div class="media">
                        <div class="media-body">
                            <h3 class="mb-0"><a href="{{ route('admin.banners.index') }}"
                                                class="text-white">{{ formatNumber($totalBanners) }}</a></h3>
                            <span class="text-uppercase font-size-theme"><a href="{{ route('admin.banners.index') }}"
                                                                            class="text-white">{{ __('Banner') }}</a></span>
                        </div>

                        <div class="ml-3 align-self-center">
                            <a href="{{ route('admin.banners.create') }}">
                                <i class="fal fa-2x fa-image text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #4776E6;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #4776E6, #8E54E9);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #4776E6, #8E54E9); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.index') }}"
                                            class="text-white">{{ formatNumber($totalContacts) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.contacts.index') }}"
                                                                        class="text-white">{{ __('Liên hệ') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.contacts.index') }}">
                            <i class="fal fa-2x fal fa-phone text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #FF512F;
background: -webkit-linear-gradient(to right, #FF512F, #DD2476);
background: linear-gradient(to right, #FF512F, #DD2476);
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.search') }}"
                                            class="text-white">{{ formatNumber($totalSearchs) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.contacts.search') }}"
                                                                        class="text-white">{{ __('Lượt tìm kiếm') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.contacts.search') }}">
                            <i class="fal fa-2x fa-search text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #56ab2f;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #a8e063, #56ab2f);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #a8e063, #56ab2f); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.subscribe_email') }}"
                                            class="text-white">{{ formatNumber($totalSubscribeEmails) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a
                                href="{{ route('admin.contacts.subscribe_email') }}"
                                class="text-white">{{ __('Email đăng ký') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.contacts.subscribe_email') }}">
                            <i class="fal fa-2x fa-envelope text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #fc00ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #00dbde, #fc00ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #00dbde, #fc00ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.mail-settings.index') }}"
                                            class="text-white">{{ formatNumber($totalSubscribeEmails) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.mail-settings.index') }}"
                                                                        class="text-white">{{ __('Chiến dịch gửi mail') }}</a></span>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ route('admin.mail-settings.index') }}">
                            <i class="fal fa-2x fa-mail-bulk text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @if(setting('analytics', 0) == \App\Enums\AnalyticsState::SHOW)
        <div class="row">
            <div class="col-md-12">
                <div class="card ajax-card" data-url="{{ route('admin.analytics') }}">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title"><i class="fal fa-chart-bar mr-2"></i> {{ __('Phân tích') }}</h6>
                    </div>

                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card ajax-card" data-url="{{ route('admin.top-referrers') }}">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title"><i class="far fa-bullseye-pointer"></i> {{ __('Tìm kiếm hàng đầu') }}
                        </h6>
                    </div>

                    <div class="card-body">

                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="card ajax-card" data-url="{{ route('admin.most-visited-pages') }}">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title"><i
                                class="far fa-bullseye-pointer"></i> {{ __('Trang truy cập nhiều nhất') }}</h6>
                    </div>

                    <div class="card-body">

                    </div>

                </div>
            </div>
        </div>
    @endif
    <div class="row">
        @if($pageTops->count() > 0)
            <div class="col-md-6">
                <div class="card" data-url="{{ route('admin.pages.index') }}">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title"><i class="fal fa-file-alt"></i> {{ __('Trang được xem nhiều nhất') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="w-100">{{ __('Tên trang') }}</th>
                                    <th>{{ __('Lượt xem') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pageTops as $pageTop)
                                    <tr>
                                        <td>
                                            <a target="_blank" href="{{ $pageTop->url() }}"
                                               class="text-default font-weight-semibold letter-icon-title">{{ $pageTop->title }}</a>
                                        </td>
                                        <td>
                                            <span class="text-muted font-size-sm">{{ $pageTop->view }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($postTops->count() > 0)
            <div class="col-md-6">
                <div class="card" data-url="{{ route('admin.posts.index') }}">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title"><i class="fal fa-edit"></i> {{ __('Bài viết được xem nhiều nhất') }}</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="w-100">{{ __('Tên bài viết') }}</th>
                                    <th>{{ __('Lượt xem') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($postTops as $postTop)
                                    <tr>
                                        <td>
                                            <a target="_blank" href="{{ $postTop->url() }}"
                                               class="text-default font-weight-semibold letter-icon-title">{{ $postTop->title }}</a>
                                        </td>
                                        <td>
                                            <span class="text-muted font-size-sm">{{ $postTop->view }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
@stop
