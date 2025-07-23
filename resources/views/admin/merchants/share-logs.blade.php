@extends('admin.layouts.master')

@section('title', __('Lịch sử chia sẻ doanh thu'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.merchants-history.index') }}
</x-page-header>
@stop

@section('page-content')
<x-card title="Lịch sử chia sẻ doanh thu">
    <div class="table-responsive">
        {!! $dataTable->table([
        'class' => 'table table-striped table-bordered nowrap',
        'style' => 'width:100%',
        ], true) !!}
    </div>
</x-card>
@stop

@push('js')
{!! $dataTable->scripts() !!}
@endpush
