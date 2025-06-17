@extends('admin.layouts.master')

@section('title', __('Tạo Shop'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop

@section('page-content')
@include('admin.merchants._form', [
'url' =>  route('admin.merchants.store'),
'contract' => new \App\Models\Merchant,

])

@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\MerchantStoreRequest', 'form'); !!}
@endpush
