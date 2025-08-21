@extends('admin.layouts.master')
@section('title', __('Trang chủ'))
@section('page-header')
<x-page-header>
    <x-slot name='title'>
        <h4><i class="icon-cube mr-2"></i> <span class="font-weight-semibold">{{ __('Trang chủ') }}</span></h4>
    </x-slot>
    {{ Breadcrumbs::render() }}
</x-page-header>
@stop
@push('css')
<link rel="stylesheet" href="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.css">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body, h4, .card-title, .table {
        font-family: 'Noto Sans', sans-serif !important;
    }
    .dashboard-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f0 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1rem;
    }
    .card-body {
        padding: 1.5rem 1rem;
    }
    .chart-container {
        width: 100%;
        height: 300px;
        margin: 0 auto;
        padding: 10px;
    }
    .large-chart-container {
        width: 100%;
        height: 450px;
        margin: 0 auto;
        padding: 10px;
    }
    .row {
        margin-bottom: 15px;
    }
    .content-wrapper {
        background: linear-gradient(to bottom, #f8f9fc 0%, #ffffff 100%);
    }
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    .table thead th {
        background: #e9ecef;
        font-weight: 600;
    }
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }
    .modal-chart {
        width: 100%;
        height: 500px;
    }
</style>
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.min.js"></script>
<script src="/backend/global_assets/js/vendors/echarts/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function () {
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('ECharts loaded:', typeof echarts !== 'undefined');

        // Store modal instances and charts
        let modalInstances = {};
        let modalCharts = {};

        // Common chart options for consistency and interactivity
        function applyCommonOptions(chart, option, chartId) {
            option.toolbox = {
                feature: {
                    saveAsImage: {title: 'Lưu ảnh'},
                    restore: {title: 'Khôi phục'},
                    myZoom: {
                        show: true,
                        title: 'Phóng to',
                        icon: 'path://M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z',
                        onclick: function () {
                            openChartModal(chartId, option);
                        }
                    }
                }
            };
            option.animationDuration = 800;
            option.animationEasing = 'cubicOut';
            option.tooltip = {
                backgroundColor: 'rgba(255,255,255,0.95)',
                borderColor: '#ddd',
                textStyle: {color: '#333', fontFamily: 'Noto Sans'},
                extraCssText: 'box-shadow: 0 2px 10px rgba(0,0,0,0.1);'
            };
            chart.setOption(option);
        }

        // Function to open chart in a modal
        function openChartModal(chartId, option) {
            // Reuse or create modal
            if (!modalInstances[chartId]) {
                // Create modal HTML
                $('body').append(`
                    <div class="modal fade" id="${chartId}-modal" tabindex="-1" aria-labelledby="${chartId}-modal-label" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="${chartId}-modal-label">${option.title.text}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="${chartId}-modal-chart" class="modal-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                // Initialize modal and chart
                modalInstances[chartId] = new bootstrap.Modal(document.getElementById(chartId + '-modal'));
                modalCharts[chartId] = echarts.init(document.getElementById(chartId + '-modal-chart'));
                // Cleanup on hide
                $('#' + chartId + '-modal').on('hidden.bs.modal', function () {
                    modalCharts[chartId].dispose();
                    modalCharts[chartId] = null;
                });
            }
            // Set option and show modal
            modalCharts[chartId].setOption(option);
            modalInstances[chartId].show();
            // Resize chart when modal is shown
            document.getElementById(chartId + '-modal').addEventListener('shown.bs.modal', function () {
                modalCharts[chartId].resize();
            });
        }

        // Merchant Chart
        var merchantChartElement = document.getElementById('merchantChart');
        if (merchantChartElement) {
            var merchantChart = echarts.init(merchantChartElement);
            var merchantOption = {
                title: {text: 'Tăng trưởng Merchant theo tháng', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', axisPointer: {type: 'line', lineStyle: {color: '#6dd5ed'}}, textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params[0].name + '<br/>' + params[0].value + ' Merchants'; }},
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {type: 'category', data: @json(array_column($merchantGrowth, 'month')), axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}},
                yAxis: {type: 'value', name: 'Số Merchant', nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}, splitLine: {lineStyle: {color: '#eee'}}},
                series: [{
                    name: 'Số Merchant',
                    type: 'line',
                    smooth: true,
                    data: @json(array_column($merchantGrowth, 'merchant_count')),
                    lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#a8e063'}, {offset: 1, color: '#56ab2f'}])},
                    areaStyle: {opacity: 0.3, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#a8e063'}, {offset: 1, color: 'rgba(255, 255, 255, 0)'}])},
                    symbol: 'circle',
                    symbolSize: 6,
                    itemStyle: {color: '#56ab2f', borderColor: '#fff', borderWidth: 2}
                }]
            };
            applyCommonOptions(merchantChart, merchantOption, 'merchantChart');
            console.log('Merchant Growth chart initialized with data:', @json($merchantGrowth));
        }

        // Contract Growth Chart
        var contractGrowthChartElement = document.getElementById('contractGrowthChart');
        if (contractGrowthChartElement) {
            var contractGrowthChart = echarts.init(contractGrowthChartElement);
            var contractGrowthOption = {
                title: {text: 'Số hợp đồng tăng trưởng theo ngày', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', axisPointer: {type: 'line', lineStyle: {color: '#6dd5ed'}}, textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { var result = params[0].name + '<br/>'; params.forEach(function (item) { result += item.seriesName + ': ' + item.value + '<br/>'; }); return result; }},
                legend: {data: ['BBNT', 'Chưa ký', 'Đã ký'], top: 'bottom', textStyle: {fontSize: 12}},
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {type: 'category', boundaryGap: false, data: @json(array_column($contractGrowth, 'date')), axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}},
                yAxis: {type: 'value', name: 'Số hợp đồng', nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}, splitLine: {lineStyle: {color: '#eee'}}},
                series: [
                    {name: 'BBNT', type: 'line', stack: 'Total', data: @json(array_column($contractGrowth, 'bbnt_count')), lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#a8e063'}, {offset: 1, color: '#56ab2f'}])}, areaStyle: {opacity: 0.4, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#a8e063'}, {offset: 1, color: 'rgba(255, 255, 255, 0)'}])}, symbol: 'circle', symbolSize: 6, itemStyle: {color: '#56ab2f', borderColor: '#fff', borderWidth: 2}},
                    {name: 'Chưa ký', type: 'line', stack: 'Total', data: @json(array_column($contractGrowth, 'not_signed_count')), lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#ff6a00'}, {offset: 1, color: '#ff9e40'}])}, areaStyle: {opacity: 0.4, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#ff6a00'}, {offset: 1, color: 'rgba(255, 255, 255, 0)'}])}, symbol: 'circle', symbolSize: 6, itemStyle: {color: '#ff6a00', borderColor: '#fff', borderWidth: 2}},
                    {name: 'Đã ký', type: 'line', stack: 'Total', data: @json(array_column($contractGrowth, 'signed_count')), lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#00c6ff'}, {offset: 1, color: '#0072ff'}])}, areaStyle: {opacity: 0.4, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#00c6ff'}, {offset: 1, color: 'rgba(255, 255, 255, 0)'}])}, symbol: 'circle', symbolSize: 6, itemStyle: {color: '#00c6ff', borderColor: '#fff', borderWidth: 2}}
                ]
            };
            applyCommonOptions(contractGrowthChart, contractGrowthOption, 'contractGrowthChart');
            console.log('Contract Growth chart initialized with data:', @json($contractGrowth));
        }

        // User Chart
        var userChartElement = document.getElementById('userChart');
        if (userChartElement) {
            var userChart = echarts.init(userChartElement);
            var userOption = {
                title: {text: 'Tăng trưởng người dùng theo tháng', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', axisPointer: {type: 'line', lineStyle: {color: '#f7971e'}}, textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params[0].name + '<br/>' + (params[0].value || 0) + ' Người dùng'; }},
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {type: 'category', data: @json(array_column($userGrowth, 'month') ?: []), axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}},
                yAxis: {type: 'value', name: 'Số người dùng', nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}, splitLine: {lineStyle: {color: '#eee'}}},
                series: [{
                    name: 'Số người dùng',
                    type: 'line',
                    smooth: true,
                    data: @json(array_column($userGrowth, 'user_count') ?: []),
                    lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#8E54E9'}, {offset: 1, color: '#4776E6'}])},
                    areaStyle: {opacity: 0.3, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#8E54E9'}, {offset: 1, color: 'rgba(255, 255, 255, 0)'}])},
                    symbol: 'circle',
                    symbolSize: 6,
                    itemStyle: {color: '#4776E6', borderColor: '#fff', borderWidth: 2}
                }]
            };
            applyCommonOptions(userChart, userOption, 'userChart');
            console.log('User Growth chart initialized with data:', @json($userGrowth));
        }

        // Total Merchant Chart (Retained Pie Chart Style)
        var totalMerchantChartElement = document.getElementById('totalMerchantChart');
        if (totalMerchantChartElement) {
            var totalMerchantChart = echarts.init(totalMerchantChartElement);
            var totalMerchantOption = {
                title: {text: 'Tổng số lượng Merchant', left: 'center', top: 20, textStyle: {fontSize: 16, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: '{a} <br/>{b}: {c}'},
                series: [{
                    name: 'Merchants',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {show: true, position: 'center', formatter: '{b}\n{c}', fontFamily: 'Noto Sans', fontSize: 18, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{ $totalMerchants ?? 0 }}, name: 'Tổng Merchant'}],
                    itemStyle: {color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#39f3f3'}, {offset: 1, color: '#40ffcc'}])}
                }]
            };
            applyCommonOptions(totalMerchantChart, totalMerchantOption, 'totalMerchantChart');
            console.log('Total Merchant chart initialized with data:', {{ $totalMerchants ?? 0 }});
        }

        // Total Income Today Chart (Retained Pie Chart Style)
        var totalIncomeTodayChartElement = document.getElementById('totalIncomeTodayChart');
        if (totalIncomeTodayChartElement) {
            var totalIncomeTodayChart = echarts.init(totalIncomeTodayChartElement);
            var totalIncomeTodayOption = {
                title: {text: 'Tổng thu nhập hôm nay', left: 'center', top: 20, textStyle: {fontSize: 16, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }},
                series: [{
                    name: 'Thu nhập',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {show: true, position: 'center', formatter: function (params) { return params.name + '\n' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }, fontFamily: 'Noto Sans', fontSize: 18, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{ $totalIncomeToday ?? 0 }}, name: 'Hôm nay'}],
                    itemStyle: {color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#00c6ff'}, {offset: 1, color: '#0072ff'}])}
                }]
            };
            applyCommonOptions(totalIncomeTodayChart, totalIncomeTodayOption, 'totalIncomeTodayChart');
            console.log('Total Income Today chart initialized with data:', {{ $totalIncomeToday ?? 0 }});
        }

        // Total Income Yesterday Chart (Retained Pie Chart Style)
        var totalIncomeYesterdayChartElement = document.getElementById('totalIncomeYesterdayChart');
        if (totalIncomeYesterdayChartElement) {
            var totalIncomeYesterdayChart = echarts.init(totalIncomeYesterdayChartElement);
            var totalIncomeYesterdayOption = {
                title: {text: 'Tổng thu nhập hôm qua', left: 'center', top: 20, textStyle: {fontSize: 16, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }},
                series: [{
                    name: 'Thu nhập',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {show: true, position: 'center', formatter: function (params) { return params.name + '\n' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }, fontFamily: 'Noto Sans', fontSize: 18, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{ $totalIncomeYesterday ?? 0 }}, name: 'Hôm qua'}],
                    itemStyle: {color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#f7971e'}, {offset: 1, color: '#ffd200'}])}
                }]
            };
            applyCommonOptions(totalIncomeYesterdayChart, totalIncomeYesterdayOption, 'totalIncomeYesterdayChart');
            console.log('Total Income Yesterday chart initialized with data:', {{ $totalIncomeYesterday ?? 0 }});
        }

        // Orders Per Hour Chart
        var orderPerHourChartElement = document.getElementById('orderPerHourChart');
        if (orderPerHourChartElement) {
            var orderPerHourChart = echarts.init(orderPerHourChartElement);
            var orderPerHourOption = {
                title: {text: 'Đơn hàng trong 24h ngày hôm qua', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}},
                xAxis: {type: 'category', data: ['0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h'], boundaryGap: false, axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}},
                yAxis: {type: 'value', name: 'Số đơn hàng', nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}, splitLine: {lineStyle: {color: '#eee'}}},
                series: [{
                    name: 'Số đơn hàng',
                    type: 'line',
                    smooth: true,
                    data: @json($hourlyOrderData),
                    lineStyle: {width: 3, color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [{offset: 0, color: '#ff6a00'}, {offset: 1, color: '#ff9e40'}])},
                    areaStyle: {opacity: 0.4, color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#ff6a00'}, {offset: 1, color: '#ffffff'}])},
                    symbol: 'circle',
                    symbolSize: 8
                }]
            };
            applyCommonOptions(orderPerHourChart, orderPerHourOption, 'orderPerHourChart');
            console.log('Order per hour chart initialized with data:', @json($hourlyOrderData));
        }

        // Top 5 Merchants This Month Chart
        var topMerchantsThisMonthChartElement = document.getElementById('topMerchantsThisMonthChart');
        if (topMerchantsThisMonthChartElement) {
            var topMerchantsThisMonthChart = echarts.init(topMerchantsThisMonthChartElement);
            var topMerchantsThisMonthOption = {
                title: {text: 'Top 5 Merchant Doanh Thu Theo Tháng', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', axisPointer: {type: 'shadow'}, textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { var result = params[0].name + '<br/>'; params.forEach(function (item) { result += item.seriesName + ': ' + item.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}) + '<br/>'; }); return result; }},
                legend: {data: ['Tháng này', 'Tháng trước'], top: 'bottom', textStyle: {fontSize: 12}},
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {type: 'value', name: 'Doanh Thu (VND)', nameLocation: 'center', nameGap: 35, nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {formatter: function (value) { return value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }, fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}},
                yAxis: {type: 'category', inverse: true, data: @json(array_column($topMerchantsThisMonth, 'name')), axisLabel: {interval: 0, rotate: 0, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}},
                series: [
                    {name: 'Tháng này', type: 'bar', data: @json(array_column($topMerchantsThisMonth, 'value')), barWidth: '20%', itemStyle: {borderRadius: 6, color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [{offset: 0, color: '#00c6ff'}, {offset: 1, color: '#0072ff'}])}, label: {show: true, position: 'right', fontFamily: 'Noto Sans', fontWeight: 'bold', fontSize: 12, formatter: function (params) { return params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }}},
                    {name: 'Tháng trước', type: 'bar', data: @json($topMerchantsLastMonth), barWidth: '20%', itemStyle: {borderRadius: 6, color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [{offset: 0, color: '#f7971e'}, {offset: 1, color: '#ffd200'}])}, label: {show: true, position: 'right', fontFamily: 'Noto Sans', fontWeight: 'bold', fontSize: 12, formatter: function (params) { return params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }}}
                ]
            };
            applyCommonOptions(topMerchantsThisMonthChart, topMerchantsThisMonthOption, 'topMerchantsThisMonthChart');
            console.log('Top Merchants This Month chart initialized with data:', @json($topMerchantsThisMonth));
        }

        // Revenue by Shop Type Chart (Retained Pie Chart Style)
        var revenueByShopTypeChartElement = document.getElementById('revenueByShopTypeChart');
        if (revenueByShopTypeChartElement) {
            var revenueByShopTypeChart = echarts.init(revenueByShopTypeChartElement);
            var revenueByShopTypeOption = {
                title: {text: 'Phân bố Doanh thu theo Shop Type', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }},
                series: [{
                    name: 'Doanh thu',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: true,
                    label: {show: true, formatter: '{b}: {d}%', fontFamily: 'Noto Sans', fontSize: 12, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 14, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: @json($revenueByShopType),
                    itemStyle: {color: function (params) { var colorList = [['#00c6ff', '#0072ff'], ['#f7971e', '#ffd200'], ['#ff6a00', '#ff9e40'], ['#6dd5ed', '#2193b0'], ['#36d1dc', '#5b86e5'], ['#4776E6', '#8E54E9'], ['#FF512F', '#DD2476'], ['#56ab2f', '#a8e063'], ['#fc00ff', '#00dbde'], ['#0052D4', '#6FB1FC']]; var gradient = colorList[params.dataIndex % colorList.length]; return new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: gradient[0]}, {offset: 1, color: gradient[1]}]); }}
                }]
            };
            applyCommonOptions(revenueByShopTypeChart, revenueByShopTypeOption, 'revenueByShopTypeChart');
            console.log('Revenue by Shop Type chart initialized with data:', @json($revenueByShopType));
        }

        // Average Revenue Per Order Chart (Retained Pie Chart Style)
        var avgRevenuePerOrderChartElement = document.getElementById('avgRevenuePerOrderChart');
        if (avgRevenuePerOrderChartElement) {
            var avgRevenuePerOrderChart = echarts.init(avgRevenuePerOrderChartElement);
            var avgRevenuePerOrderOption = {
                title: {text: 'Bình quân doanh thu/ đơn hàng', left: 'center', top: 20, textStyle: {fontSize: 16, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: function (params) { return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }},
                series: [{
                    name: 'Bình quân Doanh thu',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {show: true, position: 'center', formatter: function (params) { return params.name + '\n' + params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'}); }, fontFamily: 'Noto Sans', fontSize: 18, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: @json($avgRevenuePerOrder),
                    itemStyle: {color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#ff6a00'}, {offset: 1, color: '#ff9e40'}])}
                }]
            };
            applyCommonOptions(avgRevenuePerOrderChart, avgRevenuePerOrderOption, 'avgRevenuePerOrderChart');
            console.log('Average Revenue Per Order chart initialized with data:', @json($avgRevenuePerOrder));
        }

        // Average Transactions per Day Chart
        var avgDailyTransactionsChartElement = document.getElementById('avgDailyTransactionsChart');
        if (avgDailyTransactionsChartElement) {
            var avgDailyTransactionsChart = echarts.init(avgDailyTransactionsChartElement);
            var dates = @json($avgDailyTransactionsData['dates']);
            var counts = @json($avgDailyTransactionsData['counts']);
            var average = @json($avgDailyTransactionsData['average']);
            var avgDailyTransactionsOption = {
                title: {text: 'Số lượng giao dịch trung bình mỗi ngày', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'axis', axisPointer: {type: 'shadow'}, formatter: function (params) { var result = params[0].name + '<br/>'; params.forEach(function (item) { result += item.marker + item.seriesName + ': ' + item.value + '<br/>'; }); return result; }},
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {type: 'category', data: dates, axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}},
                yAxis: {type: 'value', name: 'Số giao dịch', nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'}, axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'}, axisLine: {lineStyle: {color: '#ccc'}}, splitLine: {lineStyle: {color: '#eee'}}},
                series: [
                    {name: 'Giao dịch/ngày', type: 'bar', data: counts, barWidth: '50%', itemStyle: {color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: '#00c6ff'}, {offset: 1, color: '#0072ff'}])}, label: {show: true, position: 'top', fontSize: 10, color: '#333'}},
                    {name: 'Trung bình', type: 'line', data: Array(dates.length).fill(average), lineStyle: {type: 'dashed', width: 2, color: '#ff6a00'}, symbol: 'none'}
                ]
            };
            applyCommonOptions(avgDailyTransactionsChart, avgDailyTransactionsOption, 'avgDailyTransactionsChart');
            console.log('Average Daily Transactions chart initialized with data:', @json($avgDailyTransactionsData));
        }

        // Shops by Shop Type Chart (Retained Pie Chart Style)
        var shopsByShopTypeChartElement = document.getElementById('shopsByShopTypeChart');
        if (shopsByShopTypeChartElement) {
            var shopsByShopTypeChart = echarts.init(shopsByShopTypeChartElement);
            var shopsByShopTypeOption = {
                title: {text: 'Số lượng Shop theo Shop Type', left: 'center', textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}},
                tooltip: {trigger: 'item', textStyle: {fontFamily: 'Noto Sans', fontSize: 12}, formatter: '{a} <br/>{b}: {c} (shops)'},
                series: [{
                    name: 'Số lượng shop',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: true,
                    label: {show: true, formatter: '{b}: {c} ({d}%)', fontFamily: 'Noto Sans', fontSize: 12, fontWeight: 'bold'},
                    emphasis: {label: {show: true, fontSize: 14, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: @json($shopsByShopType),
                    itemStyle: {color: function (params) { var colorList = [['#00c6ff', '#0072ff'], ['#f7971e', '#ffd200'], ['#ff6a00', '#ff9e40'], ['#6dd5ed', '#2193b0'], ['#36d1dc', '#5b86e5'], ['#4776E6', '#8E54E9'], ['#FF512F', '#DD2476'], ['#56ab2f', '#a8e063'], ['#fc00ff', '#00dbde'], ['#0052D4', '#6FB1FC']]; var gradient = colorList[params.dataIndex % colorList.length]; return new echarts.graphic.LinearGradient(0, 0, 0, 1, [{offset: 0, color: gradient[0]}, {offset: 1, color: gradient[1]}]); }}
                }]
            };
            applyCommonOptions(shopsByShopTypeChart, shopsByShopTypeOption, 'shopsByShopTypeChart');
            console.log('Shops by Shop Type chart initialized with data:', @json($shopsByShopType));
        }

        // Responsive resize
        window.addEventListener('resize', function () {
            merchantChart?.resize();
            contractGrowthChart?.resize();
            userChart?.resize();
            totalMerchantChart?.resize();
            totalIncomeTodayChart?.resize();
            totalIncomeYesterdayChart?.resize();
            orderPerHourChart?.resize();
            topMerchantsThisMonthChart?.resize();
            revenueByShopTypeChart?.resize();
            avgRevenuePerOrderChart?.resize();
            avgDailyTransactionsChart?.resize();
            shopsByShopTypeChart?.resize();
            Object.values(modalCharts).forEach(chart => chart?.resize());
        });
    })
</script>
@endpush

@section('page-content')
<!-- Row 1: KPIs (Pie charts) - Compact 4-column layout -->
<div class="row">
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div id="totalIncomeTodayChart" class="chart-container"></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div id="totalIncomeYesterdayChart" class="chart-container"></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div id="avgRevenuePerOrderChart" class="chart-container"></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card">
            <div id="totalMerchantChart" class="chart-container"></div>
        </div>
    </div>
</div>

<!-- Row 2: Large charts - 2 columns -->
<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="shopsByShopTypeChart" class="large-chart-container"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="revenueByShopTypeChart" class="large-chart-container"></div>
        </div>
    </div>
</div>


<!-- Row 3: Growth charts - 2 columns -->
<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="merchantChart" class="large-chart-container"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="userChart" class="large-chart-container"></div>
        </div>
    </div>
</div>

<!-- Row 4: Top Merchants and Orders - 2 columns -->
<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="topMerchantsThisMonthChart" class="large-chart-container"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="orderPerHourChart" class="large-chart-container"></div>
        </div>
    </div>
</div>

<!-- Row 5: Pie charts - 2 columns -->
<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="contractGrowthChart" class="large-chart-container"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div id="avgDailyTransactionsChart" class="large-chart-container"></div>
        </div>
    </div>
</div>

<!-- Analytics and Tables -->
@if(setting('analytics', 0) == \App\Enums\AnalyticsState::SHOW)
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card ajax-card" data-url="{{ route('admin.analytics') }}">
            <div class="card-header">
                <h6 class="card-title"><i class="fal fa-chart-bar mr-2"></i> {{ __('Phân tích') }}</h6>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card dashboard-card ajax-card" data-url="{{ route('admin.top-referrers') }}">
            <div class="card-header">
                <h6 class="card-title"><i class="far fa-bullseye-pointer mr-2"></i> {{ __('Trang truy cập nhiều nhất') }}</h6>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
@endif

<div class="row">
    @if($pageTops->count() > 0)
    <div class="col-md-6">
        <div class="card dashboard-card" data-url="{{ route('admin.pages.index') }}">
            <div class="card-header">
                <h6 class="card-title"><i class="fal fa-file-alt mr-2"></i> {{ __('Trang được xem nhiều nhất') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="w-100">{{ __('Tên trang') }}</th>
                            <th>{{ __('Lượt xem') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageTops as $pageTop)
                        <tr>
                            <td><a target="_blank" href="{{ $pageTop->url() }}" class="text-primary font-weight-semibold">{{ $pageTop->title }}</a></td>
                            <td><span class="badge badge-info">{{ $pageTop->view }}</span></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($postTops->count() > 0)
    <div class="col-md-6">
        <div class="card dashboard-card" data-url="{{ route('admin.posts.index') }}">
            <div class="card-header">
                <h6 class="card-title"><i class="fal fa-edit mr-2"></i> {{ __('Bài viết được xem nhiều nhất') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="w-100">{{ __('Tên bài viết') }}</th>
                            <th>{{ __('Lượt xem') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($postTops as $postTop)
                        <tr>
                            <td><a target="_blank" href="{{ $postTop->url() }}" class="text-primary font-weight-semibold">{{ $postTop->title }}</a></td>
                            <td><span class="badge badge-info">{{ $postTop->view }}</span></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop
