@extends('admin.layouts.master')

@section('title', __('Hợp đồng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.contracts.index') }}
</x-page-header>
@stop

@section('page-content')
@can('contracts.create')
@endcan

<x-card title="Hợp Đồng">
    {{$dataTable->table()}}
</x-card>

@stop

@push('js')
{{$dataTable->scripts()}}
<script>
</script>
@endpush
