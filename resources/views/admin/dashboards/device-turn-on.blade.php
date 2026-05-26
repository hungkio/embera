@extends('admin.layouts.master')

@section('title', __('Báo cáo máy on/off'))

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

    .dashboard-card h4 {
        font-size: 24px;
        font-weight: 700;
        color: #495057;
        margin-bottom: 4px;
    }

    .metric-card small {
        color: #878a99;
        display: block;
    }

    .chart-box {
        height: 380px;
        width: 100%;
    }
</style>
@endpush

@section('page-header')
<x-page-header>
    <x-slot name="title">
        <h4><i class="fal fa-chart-pie mr-2"></i> <span class="font-weight-semibold">Báo cáo máy on/off</span></h4>
    </x-slot>
</x-page-header>
@stop

@section('page-content')
<form method="GET" action="{{ route('admin.dashboard.device-turn-on') }}" class="row g-3 mb-4 align-items-end">
    <div class="col-md-3">
        <label for="start_date">Từ ngày</label>
        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
    </div>
    <div class="col-md-3">
        <label for="end_date">Đến ngày</label>
        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
    </div>
    <div class="col-md-2">
        <label for="group_by">Kiểu hiển thị</label>
        <select id="group_by" name="group_by" class="form-control">
            <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Theo ngày</option>
            <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Theo tuần</option>
            <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Theo tháng</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">
            <i class="fal fa-search mr-1"></i> Lọc
        </button>
        <a href="{{ route('admin.dashboard.device-turn-on') }}" class="btn btn-light">Đặt lại</a>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card dashboard-card text-center metric-card">
            <div class="card-body">
                <h6>Máy đã đặt hôm nay</h6>
                <h4>{{ formatNumber($todayStats['total']['assigned'] ?? 0) }}</h4>
                <small>Online: {{ formatNumber($todayStats['total']['online'] ?? 0) }} | Offline: {{ formatNumber($todayStats['total']['offline'] ?? 0) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card text-center metric-card">
            <div class="card-body">
                <h6>Máy Hà Nội đã đặt hôm nay</h6>
                <h4>{{ formatNumber($todayStats['hanoi']['assigned'] ?? 0) }}</h4>
                <small>Online: {{ formatNumber($todayStats['hanoi']['online'] ?? 0) }} | Offline: {{ formatNumber($todayStats['hanoi']['offline'] ?? 0) }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card text-center metric-card">
            <div class="card-body">
                <h6>Máy Tỉnh đã đặt hôm nay</h6>
                <h4>{{ formatNumber($todayStats['province']['assigned'] ?? 0) }}</h4>
                <small>Online: {{ formatNumber($todayStats['province']['online'] ?? 0) }} | Offline: {{ formatNumber($todayStats['province']['offline'] ?? 0) }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off hôm nay</h6>
                <div id="todayTotalChart" class="chart-box"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off Hà Nội hôm nay</h6>
                <div id="todayHanoiChart" class="chart-box"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off Tỉnh hôm nay</h6>
                <div id="todayProvinceChart" class="chart-box"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off theo ngày - Tổng ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <div id="rangeTotalChart" class="chart-box"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off theo ngày - Hà Nội</h6>
                <div id="rangeHanoiChart" class="chart-box"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>On/off theo ngày - Tỉnh</h6>
                <div id="rangeProvinceChart" class="chart-box"></div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
const todayStats = @json($todayStats);
const rangeStats = @json($rangeStats);

function renderTodayChart(elementId, stats, title) {
    const el = document.getElementById(elementId);
    if (!el) return;

    echarts.init(el).setOption({
        tooltip: { trigger: 'item', formatter: '{b}: {c} máy ({d}%)' },
        legend: { bottom: 0, data: ['Online', 'Offline'] },
        color: ['#22c55e', '#ef4444'],
        series: [{
            name: title,
            type: 'pie',
            radius: ['42%', '70%'],
            itemStyle: { borderRadius: 8, borderColor: '#fff', borderWidth: 2 },
            label: { formatter: '{b}\n{c} máy\n{d}%' },
            data: [
                { value: stats.online || 0, name: 'Online' },
                { value: stats.offline || 0, name: 'Offline' }
            ]
        }]
    });
}

function renderRangeChart(elementId, scope, title) {
    const el = document.getElementById(elementId);
    if (!el) return;

    const data = rangeStats.series[scope] || { online: [], offline: [] };

    echarts.init(el).setOption({
        tooltip: { trigger: 'axis' },
        legend: { top: 0, data: ['Online', 'Offline'] },
        color: ['#22c55e', '#ef4444'],
        grid: { top: 55, left: 45, right: 20, bottom: 35 },
        xAxis: { type: 'category', data: rangeStats.labels || [] },
        yAxis: { type: 'value', minInterval: 1 },
        series: [
            {
                name: 'Online',
                type: 'bar',
                stack: 'devices',
                data: data.online || [],
                label: { show: true, position: 'inside' }
            },
            {
                name: 'Offline',
                type: 'bar',
                stack: 'devices',
                data: data.offline || [],
                label: { show: true, position: 'inside' }
            }
        ]
    });
}

renderTodayChart('todayTotalChart', todayStats.total || {}, 'On/off hôm nay');
renderTodayChart('todayHanoiChart', todayStats.hanoi || {}, 'On/off Hà Nội hôm nay');
renderTodayChart('todayProvinceChart', todayStats.province || {}, 'On/off Tỉnh hôm nay');

renderRangeChart('rangeTotalChart', 'total', 'On/off theo ngày - Tổng');
renderRangeChart('rangeHanoiChart', 'hanoi', 'On/off theo ngày - Hà Nội');
renderRangeChart('rangeProvinceChart', 'province', 'On/off theo ngày - Tỉnh');
</script>
@endpush
