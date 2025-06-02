@extends('admin.layouts.master')

@section('title', __('Tạo loại danh mục'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <!-- Inner container -->
    <form action="{{ route('admin.taxonomies.store') }}" method="POST" data-block>
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
                                        name="name"
                                        :label="__('Tên')"
                                        required
                                    >
                                    </x-text-field>
                                </div>
                            </fieldset>
                        </x-card>
                        <div class="d-flex justify-content-center align-items-center action" id="action-form">
                            <a href="{{ route('admin.taxonomies.index') }}" class="btn btn-light"><i class="fal fa-arrow-left mr-2"></i>{{ __('Trở lại') }}</a>
                            <div class="btn-group ml-3">
                                <button class="btn btn-primary btn-block" data-loading><i class="fal fa-check mr-2"></i>{{ __('Lưu') }}</button>
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
    <script>
        $(document).ready(function () {
            $('#name').focus();
        });
    </script>
@endpush
