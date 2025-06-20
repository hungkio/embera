@extends('admin.layouts.master')

@section('title', __('Tạo Merchant'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop

@section('page-content')
@include('admin.merchants._form', [
'url' => route('admin.merchants.store'),
'merchant' => new \App\Models\Merchant,
'employees' => $employees,
])
@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\MerchantStoreRequest', '#form'); !!}
@endpush
