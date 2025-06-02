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
        .card-body{
            padding: 1.750rem 1rem;
        }

        .card-body .font-size-theme{
            font-size: 0.7875rem;
        }

        .jvectormap-zoomin{
            display: none;
        }

        .jvectormap-zoomout{
            display: none;
        }

        .has-bg-image{
            box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 20px;
            border-radius: 10px;
        }

        .card-box-analytics{
            box-shadow: 0px 0px 1px 1px #0c213a1a;
            border-radius: 10px;
        }
    </style>
@endpush

@push('js')
    <script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-world-mill.js"></script>
    <script src="/backend/global_assets/js/vendors/echarts/echarts.min.js"></script>
    <script src="/backend/js/chart.min.js"></script>
    <script !src="">
        $.ajaxSetup({ cache: false });
        $(function () {
            $('.card [data-action=reload]:not(.disabled)').on('click', function (e) {
                e.preventDefault();
                var $target = $(this),
                    block = $target.closest('.card');

                // Block card
                $(block).block({
                    message: '<i class="icon-spinner2 spinner"></i>',
                    overlayCSS: {
                        backgroundColor: '#fff',
                        opacity: 0.8,
                        cursor: 'wait',
                        'box-shadow': '0 0 0 1px #ddd'
                    },
                    css: {
                        border: 0,
                        padding: 0,
                        backgroundColor: 'none'
                    }
                });
                let url = $(block).data('url');
                $.get(url, function(response, status){
                    $(block).find('.card-body').html(response);
                    $(block).unblock();
                });
            });

            $('.ajax-card').each(function (index, el) {
                $(this).block({
                    message: '<i class="icon-spinner2 spinner"></i>',
                    overlayCSS: {
                        backgroundColor: '#fff',
                        opacity: 0.8,
                        cursor: 'wait',
                        'box-shadow': '0 0 0 1px #ddd'
                    },
                    css: {
                        border: 0,
                        padding: 0,
                        backgroundColor: 'none'
                    }
                });
                let $this = $(this);

                let url = $(el).data('url');
                $.get(url, function(response, status){
                    $(el).find('.card-body').html(response);
                    $this.unblock();
                });
            })
        });
    </script>
@endpush

@section('page-content')
    <div class="row">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-body has-bg-image" style="background: #0052D4;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #6FB1FC, #4364F7, #0052D4);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #6FB1FC, #4364F7, #0052D4); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-0"><a href="{{ route('admin.taxonomies.index') }}" class="text-white">{{ formatNumber($totalTaxonomy) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.taxonomies.index') }}" class="text-white">{{ __('Loại danh mục') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.pages.index') }}" class="text-white">{{ formatNumber($totalPages) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.pages.index') }}" class="text-white">{{ __('Trang') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.posts.index') }}" class="text-white">{{ formatNumber($totalPosts) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.posts.index') }}" class="text-white">{{ __('Bài viết') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.banners.index') }}" class="text-white">{{ formatNumber($totalBanners) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.banners.index') }}" class="text-white">{{ __('Banner') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.index') }}" class="text-white">{{ formatNumber($totalContacts) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.contacts.index') }}" class="text-white">{{ __('Liên hệ') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.search') }}" class="text-white">{{ formatNumber($totalSearchs) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.contacts.search') }}" class="text-white">{{ __('Lượt tìm kiếm') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.contacts.subscribe_email') }}" class="text-white">{{ formatNumber($totalSubscribeEmails) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.contacts.subscribe_email') }}" class="text-white">{{ __('Email đăng ký') }}</a></span>
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
                        <h3 class="mb-0"><a href="{{ route('admin.mail-settings.index') }}" class="text-white">{{ formatNumber($totalSubscribeEmails) }}</a></h3>
                        <span class="text-uppercase font-size-theme"><a href="{{ route('admin.mail-settings.index') }}" class="text-white">{{ __('Chiến dịch gửi mail') }}</a></span>
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
                    <h6 class="card-title"><i class="far fa-bullseye-pointer"></i> {{ __('Tìm kiếm hàng đầu') }}</h6>
                </div>

                <div class="card-body">

                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="card ajax-card" data-url="{{ route('admin.most-visited-pages') }}">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title"><i class="far fa-bullseye-pointer"></i> {{ __('Trang truy cập nhiều nhất') }}</h6>
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
                        <h6 class="card-title"><i class="fal fa-file-alt"></i> {{ __('Trang được xem nhiều nhất') }}</h6>
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
                                                <a target="_blank" href="{{ $pageTop->url() }}" class="text-default font-weight-semibold letter-icon-title">{{ $pageTop->title }}</a>
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
                                                <a target="_blank" href="{{ $postTop->url() }}" class="text-default font-weight-semibold letter-icon-title">{{ $postTop->title }}</a>
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
