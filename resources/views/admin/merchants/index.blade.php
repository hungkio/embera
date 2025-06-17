@extends('admin.layouts.master')

@section('title', __('Merchants'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.merchants.index') }}
</x-page-header>
@stop

@section('page-content')
@can('merchants.create')
@endcan

<x-card title="Merchant">
    {{$dataTable->table()}}
</x-card>

@stop

@push('js')
{{$dataTable->scripts()}}
<script>
    @can('admins.create')
    $('.buttons-create').removeClass('d-none')
    @endcan
    @can('admins.delete')
    $('.bg-danger').removeClass('d-none')
    @endcan
</script>
@endpush
