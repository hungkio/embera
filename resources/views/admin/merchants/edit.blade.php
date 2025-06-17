@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $merchant->username]))
@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.merchants.edit', $merchant) }}
</x-page-header>
@stop

@section('page-content')
@include('admin.merchants._form', [
'url' => route('admin.merchants.update', $merchant),
'merchant' => $merchant,
'method' => 'PUT'
])
@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\MerchantUpdateRequest', '#merchant-form'); !!}
@endpush
