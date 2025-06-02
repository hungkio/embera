@extends('admin.layouts.master')

@section('title', __('Thiết lập tài khoản'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <div class="d-md-flex align-items-md-start">
        <!-- Right content -->
        <div class="w-100">
            <form action="{{ route('admin.account-settings.update') }}" method="POST" data-block enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="account-information">
                        <x-card>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Thông tin tài khoản') }}
                            </legend>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right" for="first_name">
                                    {{ __("Ảnh đại diện") }} :
                                </label>
                                <div class="col-lg-9">
                                    <div id="thumbnail">
                                        <div class="single-image clearfix">
                                            <div class="image-holder" onclick="document.getElementById('avatar').click();">
                                                <img id="avatar-preview" width="170" height="170" src="{{ $currentUser->getFirstMediaUrl('avatar') ?? '/backend/global_assets/images/placeholders/placeholder.jpg'}}" />
                                                <input type="file" name="avatar" id="avatar"
                                                       class="form-control inputfile hide"
                                                       onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                                            </div>
                                        </div>
                                    </div>
                                    @error('avatar')
                                        <span class="form-text text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <x-text-field required name="first_name" :label="__('Họ')" :value="$currentUser->first_name"/>
                            <x-text-field required name="last_name" :label="__('Tên')" :value="$currentUser->last_name"/>
                            <x-text-field required name="email" :label="__('Email')" type="email" :value="$currentUser->email"/>

                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Mật khẩu') }}
                            </legend>
                            <x-text-field name="new_password" :label="__('Mật khẩu mới')" type="password" autocomplete="off"/>
                            <x-text-field name="new_password_confirmation" :label="__('Nhập lại mật khẩu')" type="password"/>
                            <x-text-field name="old_password" :label="__('Mật khẩu cũ')" type="password"/>
                        </x-card>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }} <i class="fal fa-check ml-2"></i>
                    </button>
                </div>
            </form>

        </div>
        <!-- /right content -->

    </div>
@stop
