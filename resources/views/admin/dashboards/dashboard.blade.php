@extends('admin.layouts.master')
@section('title', __('Trang chủ'))

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .dashboard-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .dashboard-card .card-body {
        padding: 20px;
    }
    .dashboard-card h6 {
        font-size: 14px;
        font-weight: 600;
        color: #666;
        margin-bottom: 10px;
        white-space: normal;
        word-break: break-word;
        text-align: center;
    }
    .dashboard-card h4 {
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }
    .chart-center {
        width: 100%;   /* hoặc 2000px tùy ý */
        height: 400px;
    }
    .chart-wrapper {
        overflow-x: auto;   /* bật scroll ngang nếu chart quá rộng */
    }
    .chart-download-btn {
        position: absolute;
        top: 6px;
        right: 8px;
        border: none;
        background: transparent;
        color: #666;
        cursor: pointer;
        font-size: 16px;
        padding: 2px 6px;
        transition: color 0.2s;
    }
    .chart-download-btn:hover {
        color: #000;
    }

    /* Tooltip hiệu ứng mượt */
    .chart-download-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        top: -28px;
        right: 0;
        background: rgba(0,0,0,0.8);
        color: #fff;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transform: translateY(4px);
        transition: all 0.2s ease;
    }
    .chart-download-btn:hover::after {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('page-header')
<x-page-header>
    <x-slot name='title'>
        <h4><i class="icon-cube mr-2"></i> <span class="font-weight-semibold">Trang chủ</span></h4>
    </x-slot>
</x-page-header>
@stop

@section('page-content')

<!-- Filter -->
<div class="mb-4" style="max-width:400px;">
    <div class="input-group">
        <span class="input-group-text">Chọn khoảng ngày</span>
        <input type="text" name="date_range"
               value="{{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}"
               class="form-control" id="dateRange">
        <button id="btnFilter" class="btn btn-primary" type="button">Lọc</button>
    </div>
</div>

<!-- I. KPIs theo khoảng ngày -->
<div class="row g-3">
    <div class="col-md-6 col-lg-3 d-flex">
        <div class="card dashboard-card text-center h-100 flex-fill">
            <div class="card-body">
                <h6>Doanh thu ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <h4>{{ formatNumber($totalRevenue) }} ₫</h4>
                <small>Kỳ trước: {{ formatNumber($prevTotalRevenue) }}
                    (<span class="{{ $revenueChangePercent >= 0 ? 'text-green' : 'text-red' }}">
                        {{ $revenueChangePercent }}%
                    </span>)
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 d-flex">
        <div class="card dashboard-card text-center h-100 flex-fill">
            <div class="card-body">
                <h6>Doanh thu mục tiêu</h6>
                <h4>{{ formatNumber($targetRevenue) }} ₫</h4>
                <small>
                    Hoàn thành: <strong>{{ $percentRevenue }}%</strong><br>
                    Còn thiếu: <span class="text-danger">{{ formatNumber($revenueRemaining) }} ₫</span>
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 d-flex">
        <div class="card dashboard-card text-center h-100 flex-fill">
            <div class="card-body">
                <h6>Giá trị trung bình đơn ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <h4>{{ formatNumber($avgOrderValue) }} ₫</h4>
                <small>Khoảng ngày đã chọn</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 d-flex">
        <div class="card dashboard-card text-center h-100 flex-fill">
            <div class="card-body">
                <h6>Doanh thu trung bình/ngày ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <h4>{{ formatNumber($avgRevenuePerDay) }} ₫</h4>
            </div>
        </div>
    </div>
</div>


<!-- II. Các chỉ số khác -->
<div class="row g-3 mt-2">
    <div class="col-md-3">
        <div class="card dashboard-card text-center h-100">
            <div class="card-body">
                <h6>Thời gian thuê pin TB ({{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }})</h6>
                <h4>{{ number_format($avgRentalHours, 2) }} giờ</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-center h-100">
            <div class="card-body">
                <h6>Số hợp đồng đang hoạt động</h6>
                <h4>{{ $activeContracts }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-center h-100">
            <div class="card-body">
                <h6>Hợp đồng sắp hết hạn</h6>
                <h4>{{ $expiringContractsCount }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-center h-100">
            <div class="card-body">
                <h6>Đã ký nhưng chưa lắp đặt</h6>
                <h4>{{ $signedNotInstalled }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Doanh thu theo ngày</h6>
    <button class="chart-download-btn" data-chart="dailyRevenueChart">
        <i class="fa fa-download"></i>
    </button>
    <div id="dailyRevenueChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Số đơn hàng và giá trị TB</h6>
    <div id="orderStatsChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Doanh thu theo mô hình kinh doanh</h6>
    <div id="shopTypeRevenueChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Doanh thu theo khu vực</h6>
    <div id="regionRevenueChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4">
  @foreach($orderStatsByRegionCharts as $code => $data)
    <div class="col-md-12">
      <div class="card dashboard-card">
        <div class="card-body">
          <h6>Thống kê {{ $code == 'HN' ? 'Hà Nội' : 'Bắc Ninh' }}</h6>
          <div id="regionChart_{{ $code }}" style="height:400px;"></div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card dashboard-card">
      <div class="card-body">
        <h6>Số đơn hàng 0 đồng (HN vs BN)</h6>
        <div id="zeroOrderChart" style="height:400px;"></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Top 10 cửa hàng</h6>
    <div id="topShopsChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Số lượng hợp đồng theo nhân viên</h6>
    <div id="contractsByAdminChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4"><div class="col-md-12"><div class="card dashboard-card"><div class="card-body">
    <h6>Doanh thu theo nhân viên BD</h6>
    <div id="revenueByAdminChart" class="chart-center"></div>
</div></div></div></div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card dashboard-card">
      <div class="card-body">
        <h6>Tăng trưởng doanh thu theo tháng</h6>
        <div id="monthlyGrowthChart" class="chart-center"></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card dashboard-card">
      <div class="card-body text-center">
        <h6>Tình trạng lắp đặt máy</h6>
        <div id="deviceBoundChart" style="height:400px;"></div>
      </div>
    </div>
  </div>
</div>

@stop
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
// === Tự động thêm nút tải xuống cho tất cả biểu đồ ===
document.querySelectorAll('[id$="Chart"], [id^="regionChart_"]').forEach(chartEl => {
    const cardBody = chartEl.closest('.card-body');
    if (!cardBody) return;

    // Đảm bảo card-body có position: relative
    cardBody.style.position = 'relative';

    // Nếu chưa có nút thì thêm vào
    if (!cardBody.querySelector('.chart-download-btn')) {
        const btn = document.createElement('button');
        btn.className = 'chart-download-btn';
        btn.innerHTML = '<i class="fa fa-download"></i>';
        btn.setAttribute('data-tooltip', 'Tải xuống biểu đồ này');
        btn.dataset.chart = chartEl.id;
        cardBody.appendChild(btn);
    }
});

// === Xử lý tải ảnh khi click (có tiêu đề trong ảnh) ===
$(document).on('click', '.chart-download-btn', async function() {
    const chartId = $(this).data('chart');
    const chartEl = document.getElementById(chartId);
    const chart = echarts.getInstanceByDom(chartEl);
    if (!chart) return alert('Không tìm thấy biểu đồ!');

    // Lấy tiêu đề
    const titleEl = $(this).siblings('h6').first();
    const titleText = titleEl.length ? titleEl.text().trim() : chartId;

    // Lấy ảnh chart
    const chartImg = new Image();
    chartImg.src = chart.getDataURL({
        type: 'png',
        pixelRatio: 2,
        backgroundColor: '#fff'
    });
    await chartImg.decode(); // đợi ảnh load xong

    // Tạo canvas mới, cao hơn 50px để chừa chỗ cho tiêu đề
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const padding = 20;
    canvas.width = chartImg.width;
    canvas.height = chartImg.height + 80;

    // Nền trắng
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Tiêu đề (đặt trên cùng)
    ctx.fillStyle = '#000';
    ctx.font = 'bold 28px "Segoe UI", Arial';
    ctx.textAlign = 'center';
    ctx.fillText(titleText, canvas.width / 2, 40);

    // Vẽ ảnh chart bên dưới
    ctx.drawImage(chartImg, 0, 60);

    // Tải ảnh
    const finalImg = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.href = finalImg;
    const safeTitle = titleText.normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '_');
    link.download = `${safeTitle}.png`;
    link.click();
});

function formatVND(value) {
    return value.toLocaleString('vi-VN') + ' ₫';
}

// --- Date Range ---
$(function () {
    $('#dateRange').daterangepicker({
        locale: { format: 'YYYY-MM-DD' },
        startDate: '{{ $startDate->format('Y-m-d') }}',
        endDate: '{{ $endDate->format('Y-m-d') }}'
    });
    $('#btnFilter').on('click', function () {
        let range = $('#dateRange').val().split(' - ');
        window.location.href = '?start_date=' + range[0] + '&end_date=' + range[1];
    });
});

// --- Daily Revenue ---
// Build data items with per-point label position
const dailyVals = @json($dailyValues);
const dailyLineData = dailyVals.map((v, i, arr) => ({
  value: v,
  label: { position: (i > 0 && v < arr[i - 1]) ? 'bottom' : 'top' }
}));

echarts.init(document.getElementById('dailyRevenueChart')).setOption({
  grid: { left: 30, right: 30, top: 50, bottom: 50, containLabel: true },
  tooltip: { trigger: 'axis' },
  xAxis: { type: 'category', data: @json($dailyDates) },
  yAxis: { type: 'value', axisLabel: { formatter: v => formatVND(v) } },
  dataZoom: [{ type: 'slider' }, { type: 'inside' }],
  series: [{
    type: 'line',
    smooth: false,
    symbol: 'circle',
    symbolSize: 8,
    data: dailyLineData,                   // <-- per-point labels here
    lineStyle: { width: 2, color: '#36A2EB' },
    itemStyle: { color: '#36A2EB' },
    label: {
      show: true,
      formatter: p => formatVND(p.value)   // position is taken from item.label.position
    },
    labelLayout: { hideOverlap: true, moveOverlap: 'shiftY' }
  }]
});


// --- Order Stats (Hybrid with fixed label) ---
const avgVals = @json($avgOrderValues);
const avgLineData = avgVals.map((v, i, arr) => ({
  value: v,
  label: { position: (i > 0 && v < arr[i - 1]) ? 'bottom' : 'top' }
}));

echarts.init(document.getElementById('orderStatsChart')).setOption({
  grid: { left: 60, right: 30, top: 50, bottom: 50, containLabel: true },
  tooltip: { trigger: 'axis' },
  legend: { data: ['Số đơn hàng', 'Giá trị TB đơn'] },
  xAxis: { type: 'category', data: @json($orderDates) },
  yAxis: [
    { type: 'value', name: 'Số đơn' },
    { type: 'value', name: 'Giá trị TB', axisLabel: { formatter: v => formatVND(v) } }
  ],
  dataZoom: [
    { type: 'slider', xAxisIndex: 0, start: 0, end: 100 },
    { type: 'inside', xAxisIndex: 0 }
  ],
  series: [
    {
      name: 'Số đơn hàng',
      type: 'bar',
      data: @json($orderCounts),
      barWidth: 25,
      itemStyle: { color: '#3f51b5' },
      label: {
        show: true,
        position: 'top',
        formatter: p => p.value.toLocaleString('vi-VN')
      },
      labelLayout: { hideOverlap: true, moveOverlap: 'shiftY' }
    },
    {
      name: 'Giá trị TB đơn',
      type: 'line',
      yAxisIndex: 1,
      smooth: true,
      symbol: 'circle',
      symbolSize: 8,
      data: avgLineData,                      // <-- per-point labels here
      lineStyle: { width: 2, color: '#FF9800' },
      itemStyle: { color: '#FF9800' },
      label: {
        show: true,
        formatter: p => formatVND(p.value)    // position comes from item.label.position
      },
      labelLayout: { hideOverlap: true, moveOverlap: 'shiftY' }
    }
  ]
});

// --- Shop Type Revenue ---
echarts.init(document.getElementById('shopTypeRevenueChart')).setOption({
    grid: {
        left: 60,
        right: 30,
        top: 50,
        bottom: 100, // chừa chỗ nhiều hơn cho nhãn dài
        containLabel: true
    },
    tooltip: { trigger: 'axis' },
    legend: { data: ['Doanh thu','Tỷ trọng %'] },
    xAxis: {
        type: 'category',
        data: @json($shopTypeNames),
        axisLabel: {
            interval: 0,   // luôn hiển thị tất cả
            rotate: 0,     // không xoay, vì ta xử lý xuống dòng
            formatter: function (value) {
                // Xuống dòng khi gặp dấu ngoặc
                value = value.replace(/\(/g, '\n(');

                // Nếu vẫn quá dài thì cắt xuống dòng mỗi 12 ký tự
                return value.length > 12
                    ? value.match(/.{1,12}/g).join('\n')
                    : value;
            }
        }
    },
    yAxis: [
        {
            type: 'value',
            axisLabel: { formatter: v => formatVND(v) }
        },
        {
            type: 'value',
            max: 100,
            axisLabel: { formatter: v => v + '%' }
        }
    ],
    dataZoom: [
        { type: 'slider', show: true, xAxisIndex: 0, height: 20, bottom: 40 },
        { type: 'inside', xAxisIndex: 0 }
    ],
    series: [
        {
            name: 'Doanh thu',
            type: 'bar',
            data: @json($shopTypeValues),
            barWidth: 45, // bar to hơn
            itemStyle: { color: '#36A2EB' },
            label: {
                show: true,
                position: 'top',
                formatter: p => formatVND(p.value)
            }
        },
        {
            name: 'Tỷ trọng %',
            type: 'line',
            yAxisIndex: 1,
            smooth: true,
            symbol: 'circle',
            symbolSize: 8,
            lineStyle: {
                width: 3,
                color: '#FF9800',
                type: 'dashed'   // <-- nét đứt
            },
            itemStyle: { color: '#FF9800' },
            data: @json($shopTypePercents),
            label: {
                show: true,
                position: 'top',
                formatter: p => p.value + '%'
            }
        }
    ]
});

// --- Region Revenue ---
echarts.init(document.getElementById('regionRevenueChart')).setOption({
    grid: { left: 60, right: 30, top: 50, bottom: 50, containLabel: true },
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: @json($regions) },
    yAxis: { type: 'value', axisLabel: { formatter: v => formatVND(v) } },
    series: [{
        type: 'bar',
        data: @json($regionRevenues),
        label: { show: true, position: 'top', formatter: p => formatVND(p.value) }
    }]
});

// --- Top Shops ---
echarts.init(document.getElementById('topShopsChart')).setOption({
    grid: {
        left: 60,
        right: 30,
        top: 50,
        bottom: 100,
        containLabel: true
    },
    tooltip: { trigger: 'axis' },
    legend: { data: ['Số đơn hàng','Doanh thu'] },
    xAxis: {
        type: 'category',
        data: @json($shopNamesTop),
        axisLabel: {
            interval: 0,
            rotate: 0,
            formatter: function (value) {
                return value.replace(/\(/g, '\n(');
            }
        }
    },
    yAxis: [
        { type: 'value', name: 'Số đơn hàng' }, // Trục Y trái
        {
            type: 'value',
            name: 'Doanh thu',
            axisLabel: { formatter: v => formatVND(v) } // Trục Y phải
        }
    ],
    dataZoom: [
        { type: 'slider', show: true, xAxisIndex: 0, height: 20, bottom: 40 },
        { type: 'inside', xAxisIndex: 0 }
    ],
    series: [
        {
            name: 'Số đơn hàng',
            type: 'bar',
            yAxisIndex: 0,  // sử dụng trục Y trái
            data: @json($topOrderCounts),
            barWidth: 30,
            itemStyle: { color: '#FF6384' },
            label: { show: true, position: 'top', formatter: p => p.value.toLocaleString('vi-VN') }
        },
        {
            name: 'Doanh thu',
            type: 'bar',
            yAxisIndex: 1,  // sử dụng trục Y phải
            data: @json($topRevenues),
            barWidth: 30,
            itemStyle: { color: '#36A2EB' },
            label: { show: true, position: 'top', formatter: p => formatVND(p.value) }
        }
    ],
    barCategoryGap: '20%',
    barGap: '50%'
});

// --- Contracts by Admin ---
echarts.init(document.getElementById('contractsByAdminChart')).setOption({
    grid: {
                left: 60,    // khoảng cách từ lề trái (px hoặc %)
                right: 30,
                top: 50,
                bottom: 50,
                containLabel: true // giữ label không bị cắt
            },
    tooltip: { trigger: 'axis' },
    legend: { data: ['Số HĐ','Tỷ trọng %'] },
    xAxis: {
        type: 'category',
        data: @json($adminNames),
        axisLabel: {
            interval: 0,  // ép hiển thị tất cả
            rotate: 0,    // không xoay
            formatter: function (value) {
                // xuống dòng ngay khi có dấu ngoặc
                return value.replace(/\(/g, '\n(');
            }
        }
    },
    yAxis: [
        { type: 'value', name: 'Số HĐ' },
        { type: 'value', name: 'Tỷ trọng %', max: 100, axisLabel: { formatter: v => v+'%' } }
    ],
    series: [
        { name: 'Số HĐ', type: 'bar', data: @json($contractCounts),
          label: { show: true, position: 'top' }},
        { name: 'Tỷ trọng %', type: 'line', yAxisIndex: 1, data: @json($adminPercents),
          label: { show: true, position: 'top', formatter: p => p.value+'%' }}
    ]
});

// --- Revenue by Admin ---
const revenueNames = @json($adminRevenueNames);
const revenueValues = @json($adminRevenueValues);
const revenuePercents = @json($adminRevenuePercents);

// Sắp xếp từ cao xuống thấp
const sorted = revenueNames.map((name, idx) => ({
    name,
    value: revenueValues[idx],
    percent: revenuePercents[idx]
})).sort((a, b) => b.value - a.value);

echarts.init(document.getElementById('revenueByAdminChart')).setOption({
    grid: {
        left: 10,   // chừa thêm để tên hiển thị thoải mái
        right: 30,
        top: 30,
        bottom: 30,
        containLabel: true
    },
    tooltip: { trigger: 'axis' },
    legend: { data: ['Doanh thu'] },
    yAxis: {
        type: 'category',
        data: sorted.map(item => item.name),
        inverse: true // người nhiều nhất ở trên
    },
    xAxis: {
        type: 'value',
        axisLabel: { formatter: v => formatVND(v) }
    },
    series: [{
        name: 'Doanh thu',
        type: 'bar',
        data: sorted.map(item => item.value),
        barWidth: 20,            // chỉnh độ dày bar
        barCategoryGap: '50%',   // khoảng cách giữa các bar (tăng % lên thì khoảng cách lớn hơn)
        label: {
            show: true,
            position: 'right',
            formatter: (p) =>
                formatVND(p.value) + ' (' + sorted[p.dataIndex].percent + '%)'
        },
        itemStyle: {
            color: '#2196f3'
        }
    }]
});

//  Số đơn hàng và giá trị trung bình đơn hàng  hai khu vực
function renderRegionChart(elId, labels, counts, totals, avgPct) {
  const maxTotal = Math.max(...totals);
  const scaledLine = avgPct.map(v => maxTotal + (v * maxTotal / 100));

  echarts.init(document.getElementById(elId)).setOption({
    tooltip: { trigger: 'axis' },
    legend: { data: ['Số đơn hàng', 'Tổng giá trị', 'Tỷ lệ % ngày'] },
    grid: { left: 60, right: 60, top: 50, bottom: 80, containLabel: true },
    xAxis: { type: 'category', data: labels },
    yAxis: [
      { type: 'value', name: 'Số đơn', position: 'left' },
      { type: 'value', name: 'Doanh thu', position: 'right', axisLabel: { formatter: v => formatVND(v) } }
    ],
    dataZoom: [
      { type: 'slider', xAxisIndex: 0, height: 20, bottom: 30 },
      { type: 'inside', xAxisIndex: 0 }
    ],
    series: [
      {
        name: 'Số đơn hàng',
        type: 'bar',
        data: counts,
        yAxisIndex: 0,
        itemStyle: { color: '#3f51b5' },
        label: { show: true, position: 'top' }
      },
      {
        name: 'Tổng giá trị',
        type: 'bar',
        data: totals,
        yAxisIndex: 1,
        itemStyle: { color: '#4caf50' },
        label: { show: true, position: 'top', formatter: p => formatVND(p.value) }
      },
      {
        name: 'Tỷ lệ % ngày',
        type: 'line',
        data: scaledLine,   // ✅ line luôn cao hơn bar
        yAxisIndex: 1,
        smooth: true,
        z: 10,
        symbol: 'circle',
        symbolSize: 8,
        itemStyle: { color: '#e91e63' },
        lineStyle: { width: 2 },
        label: {
          show: true,
          formatter: (p, i) => avgPct[p.dataIndex] + '%',
          position: 'top'
        }
      }
    ]
  });
}


// Render từng chart
@foreach($orderStatsByRegionCharts as $code => $data)
  renderRegionChart(
    "regionChart_{{ $code }}",
    @json($data['labels']),
    @json($data['counts']),
    @json($data['totals']),
    @json($data['avgPct'])
  );
@endforeach

// Số đơn hàng 0 đồng giữa 2 khu vực
function renderZeroOrderChart(labels, hnZeroCounts, hnZeroPercent, bnZeroCounts, bnZeroPercent) {
  // Tìm bar lớn nhất (số đơn hàng max) để làm chuẩn
  const maxBar = Math.max(...hnZeroCounts, ...bnZeroCounts);

  // Scale line bay lên trên (cao hơn bar max)
  const hnScaled = hnZeroPercent.map((v, i, arr) => ({
    value: maxBar + (v * maxBar / 100),
    label: {
      position: (i > 0 && v < hnZeroPercent[i - 1]) ? 'bottom' : 'top'
    }
  }));

  const bnScaled = bnZeroPercent.map((v, i, arr) => ({
    value: maxBar + (v * maxBar / 100),
    label: {
      position: (i > 0 && v < bnZeroPercent[i - 1]) ? 'bottom' : 'top'
    }
  }));

  echarts.init(document.getElementById('zeroOrderChart')).setOption({
    tooltip: { trigger: 'axis' },
    legend: { data: ['HN - 0đ', 'HN - Tỷ lệ %', 'BN - 0đ', 'BN - Tỷ lệ %'] },
    grid: { left: 60, right: 60, top: 50, bottom: 80, containLabel: true },
    xAxis: { type: 'category', data: labels },
    yAxis: [
      { type: 'value', name: 'Số đơn', position: 'left' }
    ],
    dataZoom: [
      { type: 'slider', xAxisIndex: 0, height: 20, bottom: 30 },
      { type: 'inside', xAxisIndex: 0 }
    ],
    series: [
      {
        name: 'HN - 0đ',
        type: 'bar',
        data: hnZeroCounts,
        itemStyle: { color: '#3f51b5' },
        label: { show: true, position: 'top' }
      },
      {
        name: 'HN - Tỷ lệ %',
        type: 'line',
        data: hnScaled,   // ✅ mỗi điểm có label position riêng
        smooth: true,
        z: 10,
        symbol: 'circle',
        symbolSize: 8,
        itemStyle: { color: '#3f51b5' },
        lineStyle: { width: 2, color: '#3f51b5' },
        label: {
          show: true,
          formatter: (p) => hnZeroPercent[p.dataIndex] + '%'
        }
      },
      {
        name: 'BN - 0đ',
        type: 'bar',
        data: bnZeroCounts,
        itemStyle: { color: '#e91e63' },
        label: { show: true, position: 'top' }
      },
      {
        name: 'BN - Tỷ lệ %',
        type: 'line',
        data: bnScaled,   // ✅ mỗi điểm có label position riêng
        smooth: true,
        z: 10,
        symbol: 'circle',
        symbolSize: 8,
        itemStyle: { color: '#e91e63' },
        lineStyle: { width: 2, color: '#e91e63' },
        label: {
          show: true,
          formatter: (p) => bnZeroPercent[p.dataIndex] + '%'
        }
      }
    ]
  });
}



// Gọi render
renderZeroOrderChart(
  @json($zeroOrderStats['HN']['labels'] ?? []),
  @json($zeroOrderStats['HN']['zeroCounts'] ?? []),
  @json($zeroOrderStats['HN']['zeroPercent'] ?? []),
  @json($zeroOrderStats['BN']['zeroCounts'] ?? []),
  @json($zeroOrderStats['BN']['zeroPercent'] ?? [])
);

// === Monthly Revenue Growth ===
echarts.init(document.getElementById('monthlyGrowthChart')).setOption({
    grid: { left: 60, right: 30, top: 50, bottom: 60, containLabel: true },
    tooltip: { trigger: 'axis', formatter: (params) => {
        const data = params[0];
        return `${data.axisValue}<br/>Doanh thu: ${formatVND(data.value)}`;
    }},
    xAxis: {
        type: 'category',
        data: @json($months),
        axisLabel: { rotate: 0 }
    },
    yAxis: {
        type: 'value',
        axisLabel: { formatter: v => formatVND(v) }
    },
    series: [{
        name: 'Doanh thu',
        type: 'line',
        smooth: true,
        symbol: 'circle',
        symbolSize: 8,
        lineStyle: { width: 3, color: '#4CAF50' },
        itemStyle: { color: '#4CAF50' },
        areaStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: 'rgba(76, 175, 80, 0.3)' },
                { offset: 1, color: 'rgba(76, 175, 80, 0)' }
            ])
        },
        label: {
            show: true,
            position: 'top',
            formatter: p => formatVND(p.value)
        },
        data: @json($monthlyTotals)
    }]
});
// --- Device Bound Status (from device_json) ---
echarts.init(document.getElementById('deviceBoundChart')).setOption({
  tooltip: {
    trigger: 'item',
    formatter: '{b}: {c} máy ({d}%)'
  },
  legend: {
    orient: 'horizontal',
    bottom: 10,
    data: ['Đã lắp', 'Chưa lắp']
  },
  series: [
    {
      name: 'Tình trạng máy',
      type: 'pie',
      radius: ['40%', '70%'],
      avoidLabelOverlap: false,
      itemStyle: {
        borderRadius: 8,
        borderColor: '#fff',
        borderWidth: 2
      },
      label: {
        show: true,
        position: 'outside',
        formatter: '{b}\n{d}%'
      },
      emphasis: {
        label: {
          show: true,
          fontSize: 18,
          fontWeight: 'bold'
        }
      },
      labelLine: { show: true },
      data: [
        { value: {{ $totalBoundDevices }}, name: 'Đã lắp' },
        { value: {{ $totalUnboundDevices }}, name: 'Chưa lắp' }
      ]
    }
  ]
});

</script>
@endpush
