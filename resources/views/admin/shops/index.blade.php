@extends('admin.layouts.master')

@section('title', __('Cửa Hàng'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.shops.index') }}
</x-page-header>
@stop

@section('page-content')
@can('shops.create')
@endcan

<x-card title="Cửa hàng">
    {{$dataTable->table()}}
</x-card>

@stop

@push('js')
{{$dataTable->scripts()}}
<script>

</script>
@endpush
