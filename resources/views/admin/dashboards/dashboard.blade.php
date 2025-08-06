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
    .card-body {
        padding: 1.750rem 1rem;
    }

    .chart-container {
        width: 100%;
        height: 400px;
        margin: 0 auto;
        padding: 15px;
    }

    .hourly-chart-container {
        width: 100%;
        height: 500px;
        margin: 0 auto;
        padding: 15px;
    }

    .row {
        margin-bottom: 30px;
    }

    body, h4, .card-title, .table {
        font-family: 'Noto Sans', sans-serif !important;
    }
</style>
@endpush

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/backend/global_assets/js/vendors/vector-map/jquery-jvectormap-2.0.5.min.js"></script>
<script src="/backend/global_assets/js/vendors/echarts/echarts.min.js"></script>
<script>
    $(function () {
        console.log('jQuery loaded:', typeof $ !== 'undefined');
        console.log('ECharts loaded:', typeof echarts !== 'undefined');

        // Merchant Chart (Merchant Growth by Month)
        var merchantChartElement = document.getElementById('merchantChart');
        if (merchantChartElement) {
            var merchantChart = echarts.init(merchantChartElement);
            var merchantOption = {
                title: {
                    text: 'Tăng trưởng Merchant theo tháng',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'line', lineStyle: {color: '#6dd5ed'}},
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params[0].name + '<br/>' + params[0].value + ' Merchants';
                    }
                },
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {
                    type: 'category',
                    data: @json(array_column($merchantGrowth, 'month')),
                    axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}}
                },
                yAxis: {
                    type: 'value',
                    name: 'Số Merchant',
                    nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'},
                    axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}},
                    splitLine: {lineStyle: {color: '#eee'}}
                },
                series: [{
                    name: 'Số Merchant',
                    type: 'line',
                    smooth: true,
                    data: @json(array_column($merchantGrowth, 'merchant_count')),
                    lineStyle: {
                        width: 3,
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            {offset: 0, color: '#a8e063'},
                            {offset: 1, color: '#56ab2f'}
                        ])
                    },
                    areaStyle: {
                        opacity: 0.3,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#a8e063'},
                            {offset: 1, color: 'rgba(255, 255, 255, 0)'}
                        ])
                    },
                    symbol: 'circle',
                    symbolSize: 6,
                    itemStyle: {
                        color: '#56ab2f',
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false // Tắt nhãn trên điểm để tránh rối, có thể bật lại nếu cần
                    }
                }]
            };
            merchantChart.setOption(merchantOption);
            console.log('Merchant Growth chart initialized with data:', @json($merchantGrowth));
        } else {
            console.error('Merchant chart container not found');
        }

        // Contract Growth by Day Chart
        var contractGrowthChartElement = document.getElementById('contractGrowthChart');
        if (contractGrowthChartElement) {
            var contractGrowthChart = echarts.init(contractGrowthChartElement);
            var contractGrowthOption = {
                title: {
                    text: 'Số hợp đồng tăng trưởng theo ngày',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'line', lineStyle: {color: '#6dd5ed'}},
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        var result = params[0].name + '<br/>';
                        params.forEach(function (item) {
                            result += item.seriesName + ': ' + item.value + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['BBNT', 'Chưa ký', 'Đã ký'],
                    selectedMode: true,
                    top: 'bottom'
                },
                grid: {
                    left: '5%',
                    right: '5%',
                    bottom: '20%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: @json(array_column($contractGrowth, 'date')),
                    axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}}
                },
                yAxis: {
                    type: 'value',
                    name: 'Số hợp đồng',
                    nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'},
                    axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}},
                    splitLine: {lineStyle: {color: '#eee'}}
                },
                series: [
                    {
                        name: 'BBNT',
                        type: 'line',
                        stack: 'Total',
                        data: @json(array_column($contractGrowth, 'bbnt_count')),
                        lineStyle: {
                            width: 3,
                            color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                                {offset: 0, color: '#a8e063'},
                                {offset: 1, color: '#56ab2f'}
                            ])
                        },
                        areaStyle: {
                            opacity: 0.4,
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: '#a8e063'},
                                {offset: 1, color: 'rgba(255, 255, 255, 0)'}
                            ])
                        },
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            color: '#56ab2f',
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        emphasis: {
                            itemStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    {offset: 0, color: '#a8e063'},
                                    {offset: 1, color: '#56ab2f'}
                                ]),
                                borderColor: '#fff',
                                borderWidth: 3
                            }
                        }
                    },
                    {
                        name: 'Chưa ký',
                        type: 'line',
                        stack: 'Total',
                        data: @json(array_column($contractGrowth, 'not_signed_count')),
                        lineStyle: {
                            width: 3,
                            color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                                {offset: 0, color: '#ff6a00'},
                                {offset: 1, color: '#ff9e40'}
                            ])
                        },
                        areaStyle: {
                            opacity: 0.4,
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: '#ff6a00'},
                                {offset: 1, color: 'rgba(255, 255, 255, 0)'}
                            ])
                        },
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            color: '#ff6a00',
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        emphasis: {
                            itemStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    {offset: 0, color: '#ff6a00'},
                                    {offset: 1, color: '#ff9e40'}
                                ]),
                                borderColor: '#fff',
                                borderWidth: 3
                            }
                        }
                    },
                    {
                        name: 'Đã ký',
                        type: 'line',
                        stack: 'Total',
                        data: @json(array_column($contractGrowth, 'signed_count')),
                        lineStyle: {
                            width: 3,
                            color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                                {offset: 0, color: '#00c6ff'},
                                {offset: 1, color: '#0072ff'}
                            ])
                        },
                        areaStyle: {
                            opacity: 0.4,
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: '#00c6ff'},
                                {offset: 1, color: 'rgba(255, 255, 255, 0)'}
                            ])
                        },
                        symbol: 'circle',
                        symbolSize: 6,
                        itemStyle: {
                            color: '#00c6ff',
                            borderColor: '#fff',
                            borderWidth: 2
                        },
                        emphasis: {
                            itemStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    {offset: 0, color: '#00c6ff'},
                                    {offset: 1, color: '#0072ff'}
                                ]),
                                borderColor: '#fff',
                                borderWidth: 3
                            }
                        }
                    }
                ]
            };
            contractGrowthChart.setOption(contractGrowthOption);
            console.log('Contract Growth chart initialized with data:', @json($contractGrowth));
        } else {
            console.error('Contract Growth chart container not found');
        }

        // User Chart
        var userChartElement = document.getElementById('userChart');
        if (userChartElement) {
            var userChart = echarts.init(userChartElement);
            var userOption = {
                title: {
                    text: 'Tăng trưởng người dùng theo tháng',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'line', lineStyle: {color: '#f7971e'}},
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params[0].name + '<br/>' + (params[0].value || 0) + ' Người dùng';
                    }
                },
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {
                    type: 'category',
                    data: @json(array_column($userGrowth, 'month') ? : []),
                    axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}}
                },
                yAxis: {
                    type: 'value',
                    name: 'Số người dùng',
                    nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'},
                    axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}},
                    splitLine: {lineStyle: {color: '#eee'}}
                },
                series: [{
                    name: 'Số người dùng',
                    type: 'line',
                    smooth: true,
                    data: @json(array_column($userGrowth, 'user_count') ? : []),
                    lineStyle: {
                        width: 3,
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            {offset: 0, color: '#8E54E9'},
                            {offset: 1, color: '#4776E6'}
                        ])
                    },
                    areaStyle: {
                        opacity: 0.3,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#8E54E9'},
                            {offset: 1, color: 'rgba(255, 255, 255, 0)'}
                        ])
                    },
                    symbol: 'circle',
                    symbolSize: 6,
                    itemStyle: {
                        color: '#4776E6',
                        borderColor: '#fff',
                        borderWidth: 2
                    },
                    label: {
                        show: false // Tắt nhãn trên điểm để tránh rối, có thể bật lại nếu cần
                    }
                }]
            };
            userChart.setOption(userOption);
            console.log('User Growth chart initialized with data:', @json($userGrowth));
        } else {
            console.error('User chart container not found');
        }

        // Total Merchant Pie Chart
        var totalMerchantChartElement = document.getElementById('totalMerchantChart');
        if (totalMerchantChartElement) {
            var totalMerchantChart = echarts.init(totalMerchantChartElement);
            var totalMerchantOption = {
                title: {
                    text: 'Tổng số lượng Merchant',
                    left: 'center',
                    top: 20,
                    textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: '{a} <br/>{b}: {c}'
                },
                series: [{
                    name: 'Merchants',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        position: 'center',
                        formatter: '{b}\n{c}',
                        fontFamily: 'Noto Sans',
                        fontSize: 18,
                        fontWeight: 'bold'
                    },
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{$totalMerchants ?? 0
                }
            }, name: 'Tổng Merchant'
        }],
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    {offset: 0, color: '#39f3f3'},
                    {offset: 1, color: '#40ffcc'}
                ])
            }
        }]
        }
            ;
            totalMerchantChart.setOption(totalMerchantOption);
        } else {
            console.error('Total merchant chart container not found');
        }

        // Total Income Today Chart
        var totalIncomeTodayChartElement = document.getElementById('totalIncomeTodayChart');
        if (totalIncomeTodayChartElement) {
            var totalIncomeTodayChart = echarts.init(totalIncomeTodayChartElement);
            var totalIncomeTodayOption = {
                title: {
                    text: 'Tổng thu nhập hôm nay',
                    left: 'center',
                    top: 20,
                    textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        });
                    }
                },
                series: [{
                    name: 'Thu nhập',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        position: 'center',
                        formatter: function (params) {
                            return params.name + '\n' + params.value.toLocaleString('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            });
                        },
                        fontFamily: 'Noto Sans',
                        fontSize: 18,
                        fontWeight: 'bold'
                    },
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{$totalIncomeToday ?? 0
                }
            }, name: 'Hôm nay'
        }],
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    {offset: 0, color: '#00c6ff'},
                    {offset: 1, color: '#0072ff'}
                ])
            }
        }]
        }
            ;
            totalIncomeTodayChart.setOption(totalIncomeTodayOption);
        } else {
            console.error('Total income today chart container not found');
        }

        // Total Income Yesterday Chart
        var totalIncomeYesterdayChartElement = document.getElementById('totalIncomeYesterdayChart');
        if (totalIncomeYesterdayChartElement) {
            var totalIncomeYesterdayChart = echarts.init(totalIncomeYesterdayChartElement);
            var totalIncomeYesterdayOption = {
                title: {
                    text: 'Tổng thu nhập hôm qua',
                    left: 'center',
                    top: 20,
                    textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        });
                    }
                },
                series: [{
                    name: 'Thu nhập',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        position: 'center',
                        formatter: function (params) {
                            return params.name + '\n' + params.value.toLocaleString('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            });
                        },
                        fontFamily: 'Noto Sans',
                        fontSize: 18,
                        fontWeight: 'bold'
                    },
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: [{value: {{$totalIncomeYesterday ?? 0
                }
            }, name: 'Hôm qua'
        }],
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    {offset: 0, color: '#f7971e'},
                    {offset: 1, color: '#ffd200'}
                ])
            }
        }]
        }
            ;
            totalIncomeYesterdayChart.setOption(totalIncomeYesterdayOption);
        } else {
            console.error('Total income yesterday chart container not found');
        }

        // Orders Per Hour Chart
        var orderPerHourChartElement = document.getElementById('orderPerHourChart');
        if (orderPerHourChartElement) {
            var orderPerHourChart = echarts.init(orderPerHourChartElement);
            var orderPerHourOption = {
                title: {
                    text: 'Đơn hàng trong 24h ngày hôm qua',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'axis',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12}
                },
                xAxis: {
                    type: 'category',
                    data: ['0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h'],
                    boundaryGap: false,
                    axisLabel: {interval: 0, rotate: 45, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}
                },
                yAxis: {
                    type: 'value',
                    name: 'Số đơn hàng',
                    nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'},
                    axisLabel: {fontFamily: 'Noto Sans', fontSize: 12, color: '#666'},
                    axisLine: {lineStyle: {color: '#ccc'}},
                    splitLine: {lineStyle: {color: '#eee'}}
                },
                series: [{
                    name: 'Số đơn hàng',
                    type: 'line',
                    smooth: true,
                    data: @json($hourlyOrderData),
                    lineStyle: {
                        width: 3,
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            {offset: 0, color: '#ff6a00'},
                            {offset: 1, color: '#ff9e40'}
                        ])
                    },
                    areaStyle: {
                        opacity: 0.4,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#ff6a00'},
                            {offset: 1, color: '#ffffff'}
                        ])
                    },
                    symbol: 'circle',
                    symbolSize: 8
                }]
            };
            orderPerHourChart.setOption(orderPerHourOption);
            console.log('Order per hour chart initialized with data:', @json($hourlyOrderData));
        } else {
            console.error('Order per hour chart container not found');
        }

        // Top 5 Merchants This Month Chart
        var topMerchantsThisMonthChartElement = document.getElementById('topMerchantsThisMonthChart');
        if (topMerchantsThisMonthChartElement) {
            var topMerchantsThisMonthChart = echarts.init(topMerchantsThisMonthChartElement);
            var topMerchantsThisMonthOption = {
                title: {
                    text: 'Top 5 Merchant Doanh Thu Theo Tháng',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'shadow'},
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        var result = params[0].name + '<br/>';
                        params.forEach(function (item) {
                            result += item.seriesName + ': ' + item.value.toLocaleString('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }) + '<br/>';
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Tháng này', 'Tháng trước'],
                    top: 'bottom',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12}
                },
                grid: {left: '5%', right: '5%', bottom: '20%', containLabel: true},
                xAxis: {
                    type: 'value',
                    name: 'Doanh Thu (VND)',
                    nameLocation: 'center',
                    nameGap: 35,
                    nameTextStyle: {fontFamily: 'Noto Sans', fontSize: 14, color: '#666'},
                    axisLabel: {
                        formatter: function (value) {
                            return value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'});
                        },
                        fontFamily: 'Noto Sans',
                        fontSize: 12,
                        color: '#666'
                    }
                },
                toolbox: {
                    feature: {
                        saveAsImage: {} // Thêm nút lưu ảnh
                    }
                },
                yAxis: {
                    type: 'category',
                    inverse: true,
                    data: @json(array_column($topMerchantsThisMonth, 'name')),
                    axisLabel: {interval: 0, rotate: 0, fontSize: 12, fontFamily: 'Noto Sans', color: '#666'}
                },
                series: [
                    {
                        name: 'Tháng này',
                        type: 'bar',
                        data: @json(array_column($topMerchantsThisMonth, 'value')),
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: 6,
                            color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                                {offset: 0, color: '#00c6ff'},
                                {offset: 1, color: '#0072ff'}
                            ])
                        },
                        label: {
                            show: true,
                            position: 'right',
                            fontFamily: 'Noto Sans',
                            fontWeight: 'bold',
                            fontSize: 12,
                            formatter: function (params) {
                                return params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'});
                            }
                        }
                    },
                    {
                        name: 'Tháng trước',
                        type: 'bar',
                        data: @json($topMerchantsLastMonth),
                        barWidth: '20%',
                        itemStyle: {
                            borderRadius: 6,
                            color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [
                                {offset: 0, color: '#f7971e'},
                                {offset: 1, color: '#ffd200'}
                            ])
                        },
                        label: {
                            show: true,
                            position: 'right',
                            fontFamily: 'Noto Sans',
                            fontWeight: 'bold',
                            fontSize: 12,
                            formatter: function (params) {
                                return params.value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'});
                            }
                        }
                    }
                ]
            };
            topMerchantsThisMonthChart.setOption(topMerchantsThisMonthOption);
            console.log('Top Merchants This Month chart initialized with data:', @json($topMerchantsThisMonth));
            console.log('Top Merchants Last Month chart initialized with data:', @json($topMerchantsLastMonth));
        } else {
            console.error('Top Merchants This Month chart container not found');
        }
        // Revenue by Shop Type Chart
        var revenueByShopTypeChartElement = document.getElementById('revenueByShopTypeChart');
        if (revenueByShopTypeChartElement) {
            var revenueByShopTypeChart = echarts.init(revenueByShopTypeChartElement);
            var revenueByShopTypeOption = {
                title: {
                    text: 'Phân bố Doanh thu theo Shop Type',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        });
                    }
                },
                series: [{
                    name: 'Doanh thu',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: true,
                    label: {
                        show: true,
                        formatter: '{b}: {d}%',
                        fontFamily: 'Noto Sans',
                        fontSize: 12,
                        fontWeight: 'bold'
                    },
                    emphasis: {
                        label: {show: true, fontSize: 14, fontWeight: 'bold', fontFamily: 'Noto Sans'}
                    },
                    data: @json($revenueByShopType),
                    itemStyle: {
                        color: function (params) {
                            var colorList = [
                                ['#00c6ff', '#0072ff'],
                                ['#f7971e', '#ffd200'],
                                ['#ff6a00', '#ff9e40'],
                                ['#6dd5ed', '#2193b0'],
                                ['#36d1dc', '#5b86e5'],
                                ['#4776E6', '#8E54E9'],
                                ['#FF512F', '#DD2476'],
                                ['#56ab2f', '#a8e063'],
                                ['#fc00ff', '#00dbde'],
                                ['#0052D4', '#6FB1FC']
                            ];
                            var gradient = colorList[params.dataIndex % colorList.length];
                            return new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: gradient[0]},
                                {offset: 1, color: gradient[1]}
                            ]);
                        }
                    }
                }]
            };
            revenueByShopTypeChart.setOption(revenueByShopTypeOption);
            console.log('Revenue by Shop Type chart initialized with data:', @json($revenueByShopType));
        } else {
            console.error('Revenue by Shop Type chart container not found');
        }

        // Average Revenue Per Order Chart (Pie Chart for overall average)
        var avgRevenuePerOrderChartElement = document.getElementById('avgRevenuePerOrderChart');
        if (avgRevenuePerOrderChartElement) {
            var avgRevenuePerOrderChart = echarts.init(avgRevenuePerOrderChartElement);
            var avgRevenuePerOrderOption = {
                title: {
                    text: 'Bình quân doanh thu/ đơn hàng',
                    left: 'center',
                    top: 20,
                    textStyle: {fontSize: 18, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: function (params) {
                        return params.name + '<br/>' + params.value.toLocaleString('vi-VN', {
                            style: 'currency',
                            currency: 'VND'
                        });
                    }
                },
                series: [{
                    name: 'Bình quân Doanh thu',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: true,
                        position: 'center',
                        formatter: function (params) {
                            return params.name + '\n' + params.value.toLocaleString('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            });
                        },
                        fontFamily: 'Noto Sans',
                        fontSize: 23,
                        fontWeight: 'bold',
                        lineHeight: 10
                    },
                    emphasis: {label: {show: true, fontSize: 20, fontWeight: 'bold', fontFamily: 'Noto Sans'}},
                    data: @json($avgRevenuePerOrder),
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            {offset: 0, color: '#ff6a00'},
                            {offset: 1, color: '#ff9e40'}
                        ])
                    }
                }]
            };
            avgRevenuePerOrderChart.setOption(avgRevenuePerOrderOption);
            console.log('Average Revenue Per Order chart initialized with data:', @json($avgRevenuePerOrder));
        } else {
            console.error('Average Revenue Per Order chart container not found');
        }

        // Average Transactions per Day Chart
        var avgDailyTransactionsChartElement = document.getElementById('avgDailyTransactionsChart');
        if (avgDailyTransactionsChartElement) {
            var avgDailyTransactionsChart = echarts.init(avgDailyTransactionsChartElement);
            var dates = @json($avgDailyTransactionsData['dates']);
            var counts = @json($avgDailyTransactionsData['counts']);
            var average = @json($avgDailyTransactionsData['average']);

            var avgDailyTransactionsOption = {
                title: {
                    text: 'Số lượng giao dịch trung bình mỗi ngày (Tháng này)',
                    left: 'center',
                    textStyle: {
                        fontSize: 20,
                        fontWeight: 600,
                        fontFamily: 'Noto Sans',
                        color: '#333'
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {type: 'shadow'},
                    formatter: function (params) {
                        var result = params[0].name + '<br/>';
                        params.forEach(function (item) {
                            result += item.marker + item.seriesName + ': ' + item.value + '<br/>';
                        });
                        return result;
                    }
                },
                grid: {
                    left: '5%',
                    right: '5%',
                    bottom: '20%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: dates,
                    axisLabel: {
                        interval: 0,
                        rotate: 45,
                        fontSize: 11,
                        fontFamily: 'Noto Sans',
                        color: '#666'
                    },
                    axisLine: {lineStyle: {color: '#ccc'}}
                },
                yAxis: {
                    type: 'value',
                    name: 'Số giao dịch',
                    nameTextStyle: {
                        fontFamily: 'Noto Sans',
                        fontSize: 13,
                        color: '#666'
                    },
                    axisLabel: {
                        fontFamily: 'Noto Sans',
                        fontSize: 12,
                        color: '#666'
                    },
                    axisLine: {lineStyle: {color: '#ccc'}},
                    splitLine: {lineStyle: {color: '#eee'}}
                },
                series: [
                    {
                        name: 'Giao dịch/ngày',
                        type: 'bar',
                        data: counts,
                        barWidth: '50%',
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: '#00c6ff'},
                                {offset: 1, color: '#0072ff'}
                            ])
                        },
                        label: {
                            show: true,
                            position: 'top',
                            fontSize: 10,
                            color: '#333'
                        }
                    },
                    {
                        name: 'Trung bình',
                        type: 'line',
                        data: Array(dates.length).fill(average),
                        lineStyle: {
                            type: 'dashed',
                            width: 2,
                            color: '#ff6a00'
                        },
                        symbol: 'none',
                        tooltip: {
                            show: false
                        }
                    }
                ]
            };

            avgDailyTransactionsChart.setOption(avgDailyTransactionsOption);
            console.log('Average Daily Transactions chart initialized with data:', @json($avgDailyTransactionsData));
        } else {
            console.error('Average Daily Transactions chart container not found');
        }

        // Shops by Shop Type Chart
        var shopsByShopTypeChartElement = document.getElementById('shopsByShopTypeChart');
        if (shopsByShopTypeChartElement) {
            var shopsByShopTypeChart = echarts.init(shopsByShopTypeChartElement);
            var shopsByShopTypeOption = {
                title: {
                    text: 'Số lượng Shop theo Shop Type',
                    left: 'center',
                    textStyle: {fontSize: 22, fontWeight: '500', fontFamily: 'Noto Sans', color: '#333'}
                },
                tooltip: {
                    trigger: 'item',
                    textStyle: {fontFamily: 'Noto Sans', fontSize: 12},
                    formatter: '{a} <br/>{b}: {c} (shops)'
                },
                series: [{
                    name: 'Số lượng shop',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    center: ['50%', '60%'],
                    avoidLabelOverlap: true,
                    label: {
                        show: true,
                        formatter: '{b}: {c} ({d}%)',
                        fontFamily: 'Noto Sans',
                        fontSize: 12,
                        fontWeight: 'bold'
                    },
                    emphasis: {
                        label: {show: true, fontSize: 14, fontWeight: 'bold', fontFamily: 'Noto Sans'}
                    },
                    data: @json($shopsByShopType),
                    itemStyle: {
                        color: function (params) {
                            var colorList = [
                                ['#00c6ff', '#0072ff'],
                                ['#f7971e', '#ffd200'],
                                ['#ff6a00', '#ff9e40'],
                                ['#6dd5ed', '#2193b0'],
                                ['#36d1dc', '#5b86e5'],
                                ['#4776E6', '#8E54E9'],
                                ['#FF512F', '#DD2476'],
                                ['#56ab2f', '#a8e063'],
                                ['#fc00ff', '#00dbde'],
                                ['#0052D4', '#6FB1FC'],
                                ['#a8e063', '#56ab2f'],
                                ['#ff9e40', '#ff6a00'],
                                ['#0072ff', '#00c6ff']
                            ];
                            var gradient = colorList[params.dataIndex % colorList.length];
                            return new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                {offset: 0, color: gradient[0]},
                                {offset: 1, color: gradient[1]}
                            ]);
                        }
                    }
                }]
            };
            shopsByShopTypeChart.setOption(shopsByShopTypeOption);
            console.log('Shops by Shop Type chart initialized with data:', @json($shopsByShopType));
        } else {
            console.error('Shops by Shop Type chart container not found');
        }

        // Make charts responsive
        window.addEventListener('resize', function () {
            if (merchantChartElement) merchantChart.resize();
            if (userChartElement) userChart.resize();
            if (totalMerchantChartElement) totalMerchantChart.resize();
            if (totalIncomeTodayChartElement) totalIncomeTodayChart.resize();
            if (totalIncomeYesterdayChartElement) totalIncomeYesterdayChart.resize();
            if (orderPerHourChartElement) orderPerHourChart.resize();
            if (topMerchantsThisMonthChartElement) topMerchantsThisMonthChart.resize();
            if (revenueByShopTypeChartElement) revenueByShopTypeChart.resize();
            if (avgRevenuePerOrderChartElement) avgRevenuePerOrderChart.resize();
            if (contractGrowthChartElement) contractGrowthChart.resize();
            if (avgDailyTransactionsChartElement) avgDailyTransactionsChart.resize();
            if (shopsByShopTypeChartElement) shopsByShopTypeChart.resize();
        });
    });
</script>
@endpush

@section('page-content')
<div class="row mb-4">
    <div class="col-md-12 hourly-chart-container">
        <div id="topMerchantsThisMonthChart" style="width: 100%; height: 500px;"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 hourly-chart-container">
        <div id="contractGrowthChart" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<!-- Second row: Total Income Today, Total Income Yesterday, Average Revenue Per Order, Total Merchant -->
<div class="row mb-4">
    <div class="col-md-3 chart-container">
        <div id="totalIncomeTodayChart" style="width: 100%; height: 300px;"></div>
    </div>
    <div class="col-md-3 chart-container">
        <div id="totalIncomeYesterdayChart" style="width: 100%; height: 300px;"></div>
    </div>
    <div class="col-md-3 chart-container">
        <div id="avgRevenuePerOrderChart" style="width: 100%; height: 300px;"></div>
    </div>
    <div class="col-md-3 chart-container">
        <div id="totalMerchantChart" style="width: 100%; height: 300px;"></div>
    </div>
</div>

<!-- Third row: Average Transactions per Day -->
<div class="row mb-4">
    <div class="col-md-12 hourly-chart-container">
        <div id="avgDailyTransactionsChart" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<!-- Fourth row: Merchant Growth, Orders Per Hour -->
<div class="row mb-4">
    <div class="col-md-6 chart-container">
        <div id="merchantChart" style="width: 100%; height: 600px;"></div>
    </div>
    <div class="col-md-6 chart-container">
        <div id="userChart" style="width: 100%; height: 600px;"></div>
    </div>
</div>

<!-- Fifth row: User Growth, Revenue by Shop Type -->
<div class="row mb-4" style="margin-top: 200px;">

    <div class="col-md-6 hourly-chart-container">
        <div id="shopsByShopTypeChart" style="width: 100%; height: 500px;"></div>
    </div>
    <div class="col-md-6 hourly-chart-container">
        <div id="revenueByShopTypeChart" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<!-- Sixth row: Shops by Shop Type -->
<div class="row mb-4">
    <div class="col-12 hourly-chart-container">
        <div id="orderPerHourChart" style="width: 100%; height: 570px;"></div>
    </div>
</div>

@if(setting('analytics', 0) == \App\Enums\AnalyticsState::SHOW)
<div class="row">
    <div class="col-md-12">
        <div class="card ajax-card" data-url="{{ route('admin.analytics') }}">
            <div class="card-header header-elements-inline">
                <h6 class="card-title"><i class="fal fa-chart-bar mr-2"></i> {{ __('Phân tích') }}</h6>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card ajax-card" data-url="{{ route('admin.top-referrers') }}">
            <div class="card-header header-elements-inline">
                <h6 class="card-title"><i class="far fa-bullseye-pointer"></i> {{ __('Tìm kiếm hàng đầu') }}</h6>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card ajax-card" data-url="{{ route('admin.most-visited-pages') }}">
            <div class="card-header header-elements-inline">
                <h6 class="card-title"><i class="far fa-bullseye-pointer"></i> {{ __('Trang truy cập nhiều nhất') }}
                </h6>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
@endif

<div class="row">
    @if($pageTops->count() > 0)
    <div class="col-md-6">
        <div class="card" data-url="{{ route('admin.pages.index') }}">
            <div class="card-header header-elements">
                <h6 class="card-title"><i class="fal fa-file-alt"></i> {{ __('Trang được xem nhiều nhất') }}</h6>
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
                            <td>
                                <a target="_blank" href="{{ $pageTop->url() }}"
                                   class="text-default font-weight-semibold letter-icon-title">{{ $pageTop->title }}</a>
                            </td>
                            <td>
                                <span class="text-muted font-size-sm">{{ $pageTop->view }}</span>
                            </td>
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
        <div class="card" data-url="{{ route('admin.posts.index') }}">
            <div class="card-header header-elements-inline">
                <h6 class="card-title"><i class="fal fa-edit"></i> {{ __('Bài viết được xem nhiều nhất') }}</h6>
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
                            <td>
                                <a target="_blank" href="{{ $postTop->url() }}"
                                   class="text-default font-weight-semibold letter-icon-title">{{ $postTop->title }}</a>
                            </td>
                            <td>
                                <span class="text-muted font-size-sm">{{ $postTop->view }}</span>
                            </td>
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
