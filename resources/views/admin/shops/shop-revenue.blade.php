@extends('admin.layouts.master')

@section('title', __('Danh sách Shop - Doanh thu'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.shops.revenue') }}
</x-page-header>
@stop

@section('page-content')
<x-card title="Danh sách Shop - Doanh thu">
    <form method="GET" action="{{ route('admin.shops.revenue') }}" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Từ ngày:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">Đến ngày:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn bg-blue"><i class="fal fa-filter mr-2"></i>Lọc</button>
            </div>
        </div>
    </form>
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
<script>
    $(document).ready(function() {
        // Ensure DataTable reloads with form parameters
        $('form').on('submit', function() {
            $('#dataTable').DataTable().ajax.reload();
        });
    });
</script>
@endpush
