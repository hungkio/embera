@extends('admin.layouts.master')

@section('title', __('Dashboard doanh thu'))

@push('css')
<style>
    .dashboard-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        background: #fff;
        margin-bottom: 20px;
        height: 100%;
    }

    .dashboard-card .card-body {
        padding: 20px;
        position: relative;
    }

    .dashboard-card h6 {
        font-size: 14px;
        font-weight: 700;
        color: #495057;
        margin-bottom: 10px;
    }

    .chart-box {
        height: 420px;
        width: 100%;
    }
</style>
@endpush

@section('page-header')
<x-page-header>
    <x-slot name="title">
        <h4><i class="fal fa-chart-column mr-2"></i> <span class="font-weight-semibold">Dashboard doanh thu</span></h4>
    </x-slot>
</x-page-header>
@stop

@section('page-content')
<form method="GET" action="{{ route('admin.dashboard.revenue') }}" class="row g-3 mb-4 align-items-end">
    <div class="col-md-3">
        <label for="start_date">Từ ngày</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
    </div>
    <div class="col-md-3">
        <label for="end_date">Đến ngày</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">
            <i class="fal fa-search mr-1"></i> Lọc
        </button>
        <a href="{{ route('admin.dashboard.revenue') }}" class="btn btn-light">Đặt lại</a>
    </div>
</form>

<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Doanh thu theo ngày ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <div id="dailyRevenueChart" class="chart-box"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Số lượng đơn hàng theo ngày</h6>
                <div id="dailyOrderChart" class="chart-box"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Doanh thu Hà Nội và Tỉnh theo ngày</h6>
                <div id="regionRevenueChart" class="chart-box"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Top 10 doanh thu theo shop_id</h6>
                <div id="topShopRevenueChart" class="chart-box"></div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
const dailyStats = @json($dailyStats);
const regionStats = @json($regionStats);
const topShopStats = @json($topShopStats);

function formatVND(value) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(value || 0)) + ' đ';
}

echarts.init(document.getElementById('dailyRevenueChart')).setOption({
    tooltip: {
        trigger: 'axis',
        valueFormatter: value => formatVND(value)
    },
    color: ['#2563eb'],
    grid: { top: 30, left: 70, right: 20, bottom: 40 },
    xAxis: { type: 'category', data: dailyStats.labels },
    yAxis: { type: 'value', axisLabel: { formatter: value => new Intl.NumberFormat('vi-VN').format(value) } },
    series: [{
        name: 'Doanh thu',
        type: 'bar',
        data: dailyStats.revenues,
        label: { show: true, position: 'top', formatter: params => formatVND(params.value) }
    }]
});

echarts.init(document.getElementById('dailyOrderChart')).setOption({
    tooltip: { trigger: 'axis' },
    color: ['#16a34a'],
    grid: { top: 30, left: 45, right: 20, bottom: 40 },
    xAxis: { type: 'category', data: dailyStats.labels },
    yAxis: { type: 'value', minInterval: 1 },
    series: [{
        name: 'Số đơn',
        type: 'bar',
        data: dailyStats.orderCounts,
        label: { show: true, position: 'top' }
    }]
});

echarts.init(document.getElementById('regionRevenueChart')).setOption({
    tooltip: {
        trigger: 'axis',
        valueFormatter: value => formatVND(value)
    },
    legend: { top: 0, data: ['Hà Nội', 'Tỉnh'] },
    color: ['#2563eb', '#f97316'],
    grid: { top: 55, left: 70, right: 20, bottom: 40 },
    xAxis: { type: 'category', data: regionStats.labels },
    yAxis: { type: 'value', axisLabel: { formatter: value => new Intl.NumberFormat('vi-VN').format(value) } },
    series: [
        { name: 'Hà Nội', type: 'bar', data: regionStats.hanoi },
        { name: 'Tỉnh', type: 'bar', data: regionStats.province }
    ]
});

echarts.init(document.getElementById('topShopRevenueChart')).setOption({
    tooltip: {
        trigger: 'axis',
        axisPointer: { type: 'shadow' },
        formatter: function (params) {
            const index = params[0]?.dataIndex || 0;
            const shopName = topShopStats.shopNames[index] || topShopStats.labels[index] || '';
            const lines = [`<strong>${topShopStats.labels[index]}</strong>`, shopName];

            params.forEach(item => {
                const value = item.seriesName === 'Doanh thu' ? formatVND(item.value) : item.value + ' đơn';
                lines.push(`${item.marker} ${item.seriesName}: ${value}`);
            });

            return lines.join('<br>');
        }
    },
    legend: { top: 0, data: ['Doanh thu', 'Số đơn'] },
    color: ['#2563eb', '#16a34a'],
    grid: { top: 55, left: 70, right: 55, bottom: 70 },
    xAxis: {
        type: 'category',
        data: topShopStats.labels,
        axisLabel: { interval: 0, rotate: 30 }
    },
    yAxis: [
        { type: 'value', name: 'Doanh thu', axisLabel: { formatter: value => new Intl.NumberFormat('vi-VN').format(value) } },
        { type: 'value', name: 'Số đơn', minInterval: 1 }
    ],
    series: [
        {
            name: 'Doanh thu',
            type: 'bar',
            data: topShopStats.revenues,
            label: { show: true, position: 'top', formatter: params => formatVND(params.value) }
        },
        {
            name: 'Số đơn',
            type: 'bar',
            yAxisIndex: 1,
            data: topShopStats.orderCounts,
            label: { show: true, position: 'top' }
        }
    ]
});
</script>
@endpush
