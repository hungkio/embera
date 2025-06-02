@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $banner->title]))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.banners.edit', $banner) }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.banners._form', [
        'url' =>  route('admin.banners.update', $banner),
        'banner' => $banner ?? new \App\Domain\Banner\Models\Banner,
        'method' => 'PUT'
    ])
@stop

@push('js')
    <script>
        $('.form-check-input-styled').uniform();
    </script>
    {!! JsValidator::formRequest('App\Http\Requests\Admin\BannerUpdateRequest', '#banner-form'); !!}
@endpush
