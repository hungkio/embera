@extends('admin.layouts.master')

@section('title', __('Chỉnh sửa :model', ['model' => $contract->contract_number]))
@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.contracts.edit', $contract) }}
</x-page-header>
@stop

@section('page-content')
@include('admin.contracts._form', [
'url' => route('admin.contracts.update', $contract),
'contract' => $contract,
'method' => 'PUT',
'shops' => $shops,
'merchants' => $merchants,
])


@stop

@push('js')
{!! JsValidator::formRequest('App\Http\Requests\Admin\ContractUpdateRequest', 'form'); !!}
@endpush
