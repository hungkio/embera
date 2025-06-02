@extends('admin.layouts.master')

@section('title', __('Tài khoản'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <x-card title="Tài khoản">
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
