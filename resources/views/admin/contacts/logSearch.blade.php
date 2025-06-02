@extends('admin.layouts.master')
@section('title', __('Lịch sử tìm kiếm'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    <x-card>
        {{$dataTable->table()}}
    </x-card>
@stop

@push('js')
    {{$dataTable->scripts()}}
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
