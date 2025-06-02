@extends('admin.layouts.master')

@section('title', __('Tạo vai trò'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('css')
    <style>
        @media (max-width: 767.98px) {
            .btn {
                width: auto!important;
            }
        }
    </style>
@endpush

@push('js')
    <script !src="">
        $(function () {
            $('.form-check-input-styled').uniform();
                $('.permission-group-actions > .allow-all, .permission-group-actions > .deny-all').on('click', (e) => {
                    let action = e.currentTarget.className.split(/\s+/)[2].split(/-/)[0];
                    $(e.currentTarget).closest('.permission-group')
                    .find(`.permission-${action}`)
                    .each((index, value) => {
                        $(value).prop('checked', true);
                    });
                $.uniform.update();
            });
            $('.permission-parent-actions > .allow-all, .permission-parent-actions > .deny-all').on('click', (e) => {
                let action = e.currentTarget.className.split(/\s+/)[2].split(/-/)[0];
                $(`.permission-${action}`).prop('checked', true);
                $.uniform.update();
            });
        })
    </script>
@endpush

@section('page-content')
    <form action="{{ route('admin.roles.store') }}" method="POST" data-block>
        @csrf
        <div class="d-flex align-items-start flex-column flex-md-row">

        <!-- Left content -->
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card>
                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Chung') }}
                            </legend>
                            <div class="collapse show" id="general">
                                <x-text-field
                                    name="display_name"
                                    :placeholder="__('Admin, Member')"
                                    :label="__('Tên')"
                                    required
                                >
                                </x-text-field>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Quyền') }}
                                <div class="btn-group permission-parent-actions float-right">
                                    <button type="button" class="btn btn-light allow-all">{{ __('Cho phép tất cả') }}</button>
                                    <button type="button" class="btn btn-light deny-all">{{ __('Từ chối tất cả') }}</button>
                                </div>
                            </legend>
                            <div class="collapse show" id="permissions">
                                @foreach($groupPermissions as $groupKey => $permissions)
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">

                                        <div class="clearfix"></div>

                                        <div class="permission-group mt-3">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="permission-group-head">
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-4">
                                                                <h3>{{ ucfirst(__('labels.' . $groupKey)) }}</h3>
                                                            </div>

                                                            <div class="col-md-8 col-sm-8">
                                                                <div class="btn-group permission-group-actions float-right">
                                                                    <button type="button" class="btn btn-light allow-all">{{ __('Cho phép tất cả') }}</button>
                                                                    <button type="button" class="btn btn-light deny-all">{{ __('Từ chối tất cả') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mt-3">
                                                        @foreach($permissions as $permission)
                                                            <div class="permission-row">
                                                                @error($permission->name)
                                                                <div>
                                                                    <label class="form-text text-danger text-right">{{ $message }}</label>
                                                                </div>
                                                                @enderror
                                                                <div class="row">
                                                                    <div class="col-md-5 col-sm-4">
                                                                        <span class="permission-label">
                                                                            {{ $permission->display_name }}
                                                                        </span>
                                                                    </div>

                                                                    <div class="col-md-7 col-sm-8">
                                                                        <div class="form-group float-right mr-2">
                                                                            <div class="form-check form-check-inline">
                                                                                <label class="form-check-label">
                                                                                    <input type="radio" {{ old('permissions') && old('permissions')[$permission->name] == 1 ? 'checked' : null }} name="permissions[{{ $permission->name }}]" value="1" class="form-check-input-styled permission-allow" data-fouc>
                                                                                    {{ __('Cho phép') }}
                                                                                </label>
                                                                            </div>

                                                                            <div class="form-check form-check-inline">
                                                                                <label class="form-check-label">
                                                                                    <input type="radio"
                                                                                           @empty(old('permissions'))
                                                                                               checked
                                                                                           @elseif(old('permissions')[$permission->name] == 0)
                                                                                                checked
                                                                                           @endif
                                                                                           name="permissions[{{ $permission->name }}]" value="0" class="form-check-input-styled permission-deny" data-fouc>
                                                                                    {{ __('Từ chối' )}}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                            </div>
                        </fieldset>

                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action" id="action-form">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-light"><i class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                        <div class="btn-group ml-3">
                            <button class="btn btn-primary btn-block" data-loading><i class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item submit-type" data-redirect="{{ route('admin.roles.index') }}">{{ __('Lưu và thoát') }}</a>
                                <a href="javascript:void(0)" class="dropdown-item submit-type" data-redirect="{{ route('admin.roles.create') }}">{{ __('Lưu và tạo mới') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /left content -->
    </div>
    </form>
@stop

@push('js')
@endpush
