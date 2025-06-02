@extends('admin.layouts.master')

@section('title', __('Loại danh mục'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <!-- Main charts -->
    <x-card>
        {{$dataTable->table()}}
    </x-card>
@stop

@push('js')
    {{$dataTable->scripts()}}
    <script>
        @can('taxonomies.create')
            $('.buttons-create').removeClass('d-none')
        @endcan
        @can('taxonomies.delete')
            $('.buttons-selected').removeClass('d-none')
        @endcan
    </script>
@endpush
