@extends('admin.layouts.master')

@section('title', __('Tạo Hợp Đồng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop

@section('page-content')
@include('admin.contracts._form', [
'url' =>  route('admin.contracts.store'),
'contract' => new \App\Models\Contract,

])

@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\ContractStoreRequest', 'form'); !!}
@endpush
