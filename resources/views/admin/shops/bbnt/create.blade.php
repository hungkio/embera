@extends('admin.layouts.master')

@section('title', 'Tạo BBNT')

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop

@section('page-content')
@include('admin.shops.bbnt.bbnt_preview', [
'url' => route('admin.shops.bbnt.update', $shop),
'method' => 'PUT',
'shop' => $shop,
'contract' => $contract,
'deviceSummary' => $deviceSummary,
'productSummary' => $productSummary
])
@stop
