@extends('admin.layouts.master')

@section('title', __('Vai trò'))
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
        @can('roles.create')
        $('.buttons-create').removeClass('d-none')
        @endcan
        @can('roles.delete')
        $('.bg-danger').removeClass('d-none')
        @endcan
    </script>
@endpush
