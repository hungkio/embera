@extends('admin.layouts.master')

@section('title', __('Lịch sử thao tác'))
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
            @cannot('log-activities.destroy')
            $('.buttons-selected').remove()
            @endcan
        })
    </script>
@endpush
