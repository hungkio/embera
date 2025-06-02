@extends('admin.layouts.master')

@section('title', __('Tạo Quảng Cáo'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.banners._form', [
        'url' =>  route('admin.banners.store'),
        'banner' => new \App\Domain\Banner\Models\Banner,
    ])
@stop

@push('js')
    <script>
        $('.form-check-input-styled').uniform();
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Admin\BannerStoreRequest', '#banner-form'); !!}
@endpush
