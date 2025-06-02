@extends('admin.layouts.master')

@section('title', 'Cài đặt chung')

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('js')
    <script>
        jQuery(document).ready(function () {
            $('.select-store').select2();
            $('.form-check-input').uniform();
        });

        function setTaxonSelect(selector) {
            function formatTaxon (taxon) {
                return taxon.pretty_name;
            }
            if ($(selector).length > 0) {
                $(selector).select2({
                    placeholder: "<?php echo e(__('Chọn danh mục')); ?>",
                    width: '100%',
                    ajax: {
                        url: "<?php echo e(route('admin.taxons.search')); ?>",
                        datatype: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;

                            return {
                                results: data.data,
                                pagination: {
                                    more: (params.page * 15) < data.total
                                }
                            };
                        },
                    },
                    templateResult: formatTaxon,
                    templateSelection: function(item) { return item.pretty_name || item.text; }
                });
            }
        }
    </script>
@endpush
@section('page-content')
    <!-- Inner container -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="setting-form">
        @csrf
        <div class="d-flex align-items-start flex-column flex-md-row">

            <!-- Left content -->
            <div class="w-100 order-2 order-md-1 left-content">

                <div class="row">
                    <div class="col-md-12">
                        <x-collapse-card>
                            <fieldset>
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{ __('Cài đặt cửa hàng') }}
                                </legend>

                                <div class="collapse show" id="general">
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right"> {{ __('Logo') }}:</label>
                                        <div class="col-lg-9">
                                            <div id="thumbnail">
                                                <div class="single-image">
                                                    <div class="image-holder" onclick="document.getElementById('store_logo').click();">
                                                        <img id="store-logo-preview" width="170" height="170" src="{{ setting('store_logo') ? \Storage::url(setting('store_logo')): '/backend/global_assets/images/placeholders/placeholder.jpg'}}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="file" name="store_logo" id="store_logo"
                                                   class="form-control inputfile hide"
                                                   onchange="document.getElementById('store-logo-preview').src = window.URL.createObjectURL(this.files[0])">
                                            @error('store_logo')
                                                <span class="form-text text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right">{{ __('Favicon') }}:</label>
                                        <div class="col-lg-9">
                                            <div id="thumbnail">
                                                <div class="single-image clearfix">
                                                    <div class="image-holder" onclick="document.getElementById('store_favicon').click();">
                                                        <img id="store-favicon-preview" width="170" height="170" src="{{ setting('store_favicon') ? \Storage::url(setting('store_favicon')): '/backend/global_assets/images/placeholders/placeholder.jpg'}}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="file" name="store_favicon" id="store_favicon"
                                                   class="form-control inputfile hide"
                                                   onchange="document.getElementById('store-favicon-preview').src = window.URL.createObjectURL(this.files[0])">
                                            @error('store_favicon')
                                                <span class="form-text text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <x-text-field
                                        name="store_name"
                                        :label="__('Tên cửa hàng')"
                                        :placeholder="__('An An')"
                                        :value="setting('store_name')"
                                        required
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="store_slogan"
                                        :label="__('Khẩu hiệu')"
                                        :placeholder="__('Mãi vươn xa...')"
                                        :value="setting('store_slogan')"
                                        required
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="store_description"
                                        :label="__('Mô tả cửa hàng')"
                                        :placeholder="__('Mô tả cửa hàng')"
                                        :value="setting('store_description')"
                                        required
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="store_phone"
                                        :label="__('Hotline liên hệ')"
                                        :placeholder="__('0123456789')"
                                        :value="setting('store_phone')"
                                        required
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="store_email"
                                        :label="__('Email liên hệ')"
                                        :placeholder="__('thanhphong@gmail.com')"
                                        :value="setting('store_email')"
                                        required
                                    >
                                    </x-text-field>
                                    <x-text-field
                                        name="store_address"
                                        :label="__('Địa chỉ liên hệ')"
                                        :placeholder="__('Hà Nội, Việt Nam')"
                                        :value="setting('store_address')"
                                        required
                                    >
                                    </x-text-field>

                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        {{ __('Danh mục chức năng') }}
                                    </legend>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="post_taxonomy">{{ __('Danh mục cho bài viết') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="post_taxonomy" id="post_taxonomy" class="form-control js-select2" data-placeholder="{{ __('Chọn danh mục') }}">
                                                @foreach($taxonomies as $taxonomy)
                                                    <option {{ setting('post_taxonomy', 1) == $taxonomy->id ? 'selected' : null }} value="{{ $taxonomy->id }}">{{ $taxonomy->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        {{ __('Menu trang chủ') }}
                                    </legend>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="ad_taxonomy">{{ __('Menu đầu trang') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="menu_header" id="menu_header" class="form-control js-select2" data-placeholder="{{ __('Chọn menu') }}">
                                                @foreach($menus as $menu)
                                                    <option {{ setting('menu_header', 1) == $menu->id ? 'selected' : null }} value="{{ $menu->id }}">{{ $menu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="ad_taxonomy">{{ __('Menu cuối trang 1') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="menu_footer_1" id="menu_footer_1" class="form-control js-select2" data-placeholder="{{ __('Chọn menu') }}">
                                                @foreach($menus as $menu)
                                                    <option {{ setting('menu_footer_1', 1) == $menu->id ? 'selected' : null }} value="{{ $menu->id }}">{{ $menu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="ad_taxonomy">{{ __('Menu cuối trang 2') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="menu_footer_2" id="menu_footer_2" class="form-control js-select2" data-placeholder="{{ __('Chọn menu') }}">
                                                @foreach($menus as $menu)
                                                    <option {{ setting('menu_footer_2', 1) == $menu->id ? 'selected' : null }} value="{{ $menu->id }}">{{ $menu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        {{ __('Mạng xã hội') }}
                                    </legend>

                                    <x-text-field
                                        name="link_facebook"
                                        :label="__('Link Facebook')"
                                        :placeholder="__('https://facebook.com/....')"
                                        :value="setting('link_facebook')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="link_youtube"
                                        :label="__('Link Youtube')"
                                        :placeholder="__('https://youtube.com/....')"
                                        :value="setting('link_youtube')"
                                    >
                                    </x-text-field>
                                    <x-text-field
                                        name="link_zalo"
                                        :label="__('Link Zalo')"
                                        :placeholder="__('https://zalo.vn/....')"
                                        :value="setting('link_zalo')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="link_twitter"
                                        :label="__('Link Twitter')"
                                        :placeholder="__('https://twitter.com/....')"
                                        :value="setting('link_twitter')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="link_instagram"
                                        :label="__('Link Instagram')"
                                        :placeholder="__('https://instagram.com/....')"
                                        :value="setting('link_instagram')"
                                    >
                                    </x-text-field>

                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        {{ __('Cấu hình gửi mail') }}
                                    </legend>

                                    <x-text-field
                                        name="mail_from_address"
                                        :label="__('Địa chỉ gửi Mail (Mail)')"
                                        placeholder=""
                                        :value="setting('mail_from_address')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="mail_from_name"
                                        :label="__('Tên người gửi Mail (Name)')"
                                        placeholder=""
                                        :value="setting('mail_from_name')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="mail_host"
                                        :label="__('Máy chủ Mail (Host)')"
                                        placeholder=""
                                        :value="setting('mail_host')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="mail_port"
                                        :label="__('Cổng Mail (Port)')"
                                        placeholder=""
                                        :value="setting('mail_port')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="mail_username"
                                        :label="__('Tài khoản Mail (Username)')"
                                        placeholder=""
                                        :value="setting('mail_username')"
                                    >
                                    </x-text-field>

                                    <x-text-field
                                        name="mail_password"
                                        type="password"
                                        :label="__('Mật khẩu mail (Password)')"
                                        placeholder=""
                                        :value="setting('mail_password')"
                                    >
                                    </x-text-field>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="mail_encryption">{{ __('Mã hóa Mail (Encryption)') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="mail_encryption" class="form-control" data-placeholder="{{ __('Vui lòng chọn') }}">
                                                <option value="">{{ __('Vui lòng chọn') }}</option>
                                                <option value="ssl" {{ setting('mail_encryption') == 'ssl' ? 'selected' : null }}>SSL</option>
                                                <option value="tls" {{ setting('mail_encryption') == 'tls' ? 'selected' : null }}>TLS</option>
                                            </select>
                                        </div>
                                    </div>

                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        {{ __('Cài đặt khác') }}
                                    </legend>
                                    <x-text-field
                                        name="store_menu_level"
                                        :label="__('Tùy chỉnh số cấp menu')"
                                        :placeholder="__('3')"
                                        :value="setting('store_menu_level')"
                                        required
                                    >
                                    </x-text-field>
                                    <x-text-field
                                        name="store_taxon_level"
                                        :label="__('Tùy chỉnh số cấp danh mục')"
                                        :placeholder="__('3')"
                                        :value="setting('store_taxon_level')"
                                        required
                                    >
                                    </x-text-field>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="ad_taxonomy">{{ __('Banner') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="store_banner" id="banner" class="form-control js-select2" data-placeholder="{{ __('Hiển thị') }}">
                                                @foreach(\App\Domain\Banner\Models\Banner::BANNER_MODE as $key => $mode)
                                                    <option {{ setting('store_banner', 1) == $key ? 'selected' : null }} value="{{ $key }}"> {{ $mode }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="ad_taxonomy">{{ __('Analytics') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="analytics" id="analytics" class="form-control js-select2">
                                                @foreach(\App\Enums\AnalyticsState::ANALYTIC_MODE as $key => $mode)
                                                    <option {{ setting('analytics', 0) == $key ? 'selected' : null }} value="{{ $key }}"> {{ $mode }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="language">{{ __('Ngôn ngữ hiển thị') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="language" id="language" class="form-control js-select2" data-placeholder="{{ __('Chọn ngôn ngữ') }}">
                                                <option {{ setting('language', 'vi') == 'vi' ? 'selected' : null }} value="vi"> Việt Nam </option>
                                                <option {{ setting('language', 'vi') == 'en' ? 'selected' : null }} value="en"> English </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="date_format">{{ __('Hiển thị ngày tháng') }}:</label>

                                        <div class="col-lg-9">
                                            <select name="date_format" id="date_format" class="form-control js-select2" data-placeholder="{{ __('Chọn định dạng') }}">
                                                <option value="d-m-Y" {{ setting('date_format', 'H:i:s Y-m-d') == 'd-m-Y' ? 'selected' : null}}>dd-mm-yyyy</option>
                                                <option value="m-d-Y" {{ setting('date_format', 'H:i:s Y-m-d') == 'm-d-Y' ? 'selected' : null}}>mm-dd-yyyy</option>
                                                <option value="d/m/Y" {{ setting('date_format', 'H:i:s Y-m-d') == 'd/m/Y' ? 'selected' : null}}>dd/mm/yyyy</option>
                                                <option value="m/d/Y" {{ setting('date_format', 'H:i:s Y-m-d') == 'm/d/Y' ? 'selected' : null}}>mm/dd/yyyy</option>
                                                <option value="Y-m-d H:i:s" {{ setting('date_format', 'H:i:s Y-m-d') == 'Y-m-d H:i:s' ? 'selected' : null}}>Y-m-d H:i:s</option>
                                                <option value="H:i:s Y-m-d" {{ setting('date_format', 'H:i:s Y-m-d') == 'H:i:s Y-m-d' ? 'selected' : null}}>H:i:s Y-m-d</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="custom_scripts">{{ __('Tùy chỉnh giao diện') }}:</label>
                                        <div class="col-lg-9">
                                            <textarea name="custom_styles" id="custom_styles" cols="30" rows="10" class="form-control">{{ setting('custom_styles') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-2 col-form-label text-lg-right" for="custom_scripts">{{ __('Tùy chỉnh Scripts') }}:</label>
                                        <div class="col-lg-9">
                                            <textarea name="custom_scripts" id="custom_scripts" cols="30" rows="10" class="form-control">{{ setting('custom_scripts') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </x-collapse-card>
                        <div class="d-flex justify-content-center align-items-center action-div" id="action-form">
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary btn-block" data-loading>{{ __('Lưu') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /left content -->
        </div>
        <!-- /inner container -->
    </form>
@stop
@push('js')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\SettingUpdateRequest', '#setting-form'); !!}
@endpush
