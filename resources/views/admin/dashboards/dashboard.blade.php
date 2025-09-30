@extends('admin.layouts.master')
@section('title', __('Trang chủ'))

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
    }
    .dashboard-card h4 {
        font-size: 22px;
        font-weight: bold;
        color: #333;
    }
    .chart-container {
        width: 100%;
        height: 300px;
        margin: 0 auto;
        padding: 10px;
    }
    .chart-center {
        max-width: 1000px;   /* giới hạn max width để chart không tràn */
        width: 100%;         /* responsive */
        height: 400px;       /* chiều cao cố định */
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
                <h6>Giá trị trung bình đơn</h6>
                <h4>{{ formatNumber($avgOrderValue) }} ₫</h4>
                <small>Khoảng ngày đã chọn</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 d-flex">
        <div class="card dashboard-card text-center h-100 flex-fill">
            <div class="card-body">
                <h6>Doanh thu trung bình/ngày</h6>
                <h4>{{ formatNumber($avgRevenuePerDay) }} ₫</h4>
            </div>
        </div>
    </div>
</div>

<!-- II. Các chỉ số khác -->
<div class="row g-3 mt-2">
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <h6>Thời gian thuê pin TB</h6>
                <h4>{{ number_format($avgRentalHours, 2) }} giờ</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <h6>Số hợp đồng đang hoạt động</h6>
                <h4>{{ $activeContracts }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <h6>Hợp đồng sắp hết hạn</h6>
                <h4>{{ $expiringContractsCount }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card text-center">
            <div class="card-body">
                <h6>Đã ký nhưng chưa lắp đặt</h6>
                <h4>{{ $signedNotInstalled }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- III. Charts theo filter -->
<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Doanh thu theo ngày</h6>
                <canvas id="dailyRevenueChart" width="1200" height="400" style="max-width:100%;"></canvas>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Số đơn hàng và giá trị trung bình đơn</h6>
                <canvas id="orderStatsChart" width="1200" height="400" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Doanh thu theo mô hình kinh doanh</h6>
                <canvas id="shopTypeRevenueChart" width="1200" height="400" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Doanh thu theo khu vực</h6>
                <div id="regionRevenueChart" width="1200" height="400" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Top 10 cửa hàng có doanh thu cao nhất</h6>
                <div class="d-flex justify-content-center">
                <canvas id="topShopsChart" class="chart-center" width="1200" height="400" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card dashboard-card">
            <div class="card-body">
                <h6>Số lượng hợp đồng theo từng nhân viên</h6>
                <div class="d-flex justify-content-center">
                <canvas id="contractsByAdminChart" class="chart-center" width="1200" height="400" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card dashboard-card">
      <div class="card-body">
        <h6>Doanh thu theo nhân viên BD</h6>
        <div class="d-flex justify-content-center">
        <canvas id="revenueByAdminChart" width="1550" height="400" class="d-block mx-auto"></canvas>
      </div>
    </div>
  </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
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

    // Register Chart.js datalabels plugin
    Chart.register(ChartDataLabels);

    // Hàm định dạng VND
    function formatVND(value) {
        return value.toLocaleString('vi-VN', { minimumFractionDigits: 0 }) + ' ₫';
    }

    // --- Chart: Daily Revenue ---
    const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(dailyRevenueCtx, {
        type: 'line',
        data: {
            labels: @json($dailyDates),
            datasets: [{
                label: 'Doanh thu (VND)',
                data: @json($dailyValues),
                borderColor: '#36A2EB',
                pointBackgroundColor: '#36A2EB',
                pointBorderColor: '#36A2EB',
                borderWidth: 2,
                fill: false,
                tension: 0,
                datalabels: {
                    anchor: 'end',
                    align: function(context) {
                        const data = context.dataset.data;
                        const index = context.dataIndex;
                        if (index === 0) return 'top';
                        return data[index] >= data[index - 1] ? 'top' : 'bottom';
                    },
                    color: '#36A2EB',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: function(value) {
                        // Giữ nguyên định dạng VND đầy đủ
                        return formatVND(value);
                    }
                }
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                datalabels: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            // hiển thị theo VND đầy đủ
                            return formatVND(value);
                        }
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    });

    // --- Chart: Order Stats ---
    const orderStatsCtx = document.getElementById('orderStatsChart').getContext('2d');
    new Chart(orderStatsCtx, {
        type: 'bar',
        data: {
            labels: @json($orderDates),
            datasets: [
                {
                    label: 'Số đơn hàng',
                    data: @json($orderCounts),
                    backgroundColor: '#2196f3',
                    yAxisID: 'y',
                    order: 2,
                    datalabels: {
                        anchor: 'end',
                        align: 'start',
                        color: '#fff',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value.toLocaleString('vi-VN')
                    }
                },
                {
                    type: 'line',
                    label: 'Giá trị TB đơn (VND)',
                    data: @json($avgOrderValues),
                    borderColor: '#3f51b5',
                    borderWidth: 2,
                    fill: false,
                    yAxisID: 'y1',
                    tension: 0.2,
                    order: 1,
                    datalabels: {
                        color: '#3f51b5',
                        font: { weight: 'bold', size: 12 },
                        align: function(context) {
                            const data = context.dataset.data;
                            const index = context.dataIndex;
                            if (index === 0) return 'top';
                            return data[index] >= data[index - 1] ? 'top' : 'bottom';
                        },
                        anchor: function(context) {
                            const data = context.dataset.data;
                            const index = context.dataIndex;
                            if (index === 0) return 'end';
                            return data[index] >= data[index - 1] ? 'end' : 'start';
                        },
                        formatter: function(value) {
                            return value.toLocaleString('de-DE', { minimumFractionDigits: 0 }) + ' ₫';
                        }
                    }
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: { display: true },
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { beginAtZero: true },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    ticks: {
                        callback: (value) => value >= 1000 ? (Math.round(value/1000) + 'K') : value
                    }
                }
            }
        }
    });

   // --- Chart: Shop Type Revenue with Cumulative Line ---
   // Đăng ký plugin datalabels
     Chart.register(ChartDataLabels);

     // Hàm format tiền VND (bạn đã có)
     function formatVND(value) {
         return value.toLocaleString('vi-VN', { minimumFractionDigits: 0 }) + ' ₫';
     }

     // --- Chart: Doanh thu theo mô hình kinh doanh (bar + line % theo từng loại) ---
     const shopTypeCtx = document.getElementById('shopTypeRevenueChart').getContext('2d');

     new Chart(shopTypeCtx, {
       data: {
         labels: @json($shopTypeNames),
         datasets: [
           {
             type: 'bar',
             label: 'Doanh thu (VND)',
             data: @json($shopTypeValues),
             yAxisID: 'y',
             backgroundColor: '#36A2EB',
             order: 2,
             datalabels: {
               anchor: 'end',
               align: 'end',
               color: '#333',
               font: { weight: 'bold', size: 12 },
               formatter: (val) => formatVND(val)
             }
           },
           {
               type: 'line',
                   label: 'Tỷ trọng (%)',
                   data: @json($shopTypePercents),
                   yAxisID: 'y1',
                   borderColor: '#ffffff',          // line màu trắng
                   backgroundColor: '#ffffff',
                   borderDash: [6, 6],              // nét đứt (6px vẽ, 6px hở)
                   borderWidth: 2,
                   tension: 0,                      // line cứng
                   fill: false,
                   pointRadius: 3,
                   pointBackgroundColor: '#ffffff', // chấm trắng
                   pointBorderColor: '#0d47a1',     // viền chấm xanh đậm
                   pointHoverRadius: 4,
                   datalabels: {
                       anchor: 'end',
                       align: 'top',
                       color: '#0d47a1',            // chữ xanh đậm
                       font: { weight: 'bold', size: 11 },
                       formatter: (val) => val + '%'
               }
           }
         ]
       },
       options: {
         responsive: true,
         plugins: {
           datalabels: { display: true },
           legend: { display: true, position: 'top' },
           tooltip: {
             callbacks: {
               label: (ctx) => {
                 const ds = ctx.dataset;
                 if (ds.yAxisID === 'y') return `${ds.label}: ${formatVND(ctx.raw)}`;
                 return `${ds.label}: ${ctx.raw}%`;
               }
             }
           }
         },
         scales: {
           y: {
             beginAtZero: true,
             title: { display: true, text: 'Doanh thu (VND)' },
             ticks: {
               callback: (v) => formatVND(v)
             }
           },
           y1: {
             beginAtZero: true,
             max: 100,                 // tổng = 100%
             position: 'right',
             grid: { drawOnChartArea: false },
             title: { display: true, text: 'Tỷ trọng (%)' },
             ticks: { callback: (v) => v + '%' }
           },
           x: {
             ticks: { autoSkip: false, maxRotation: 35, minRotation: 0 }
           }
         }
       }
     });


    // --- ECharts: Region Revenue ---
    const regionChart = echarts.init(document.getElementById('regionRevenueChart'));
    regionChart.setOption({
        dataset: {
            dimensions: ['region', 'value'],
            source: @json($regionSource)
        },
        xAxis: { type: 'category' },
        yAxis: {
            type: 'value',
            axisLabel: {
                formatter: function(value) {
                    return formatVND(value);
                }
            }
        },
        series: [{
            type: 'bar',
            label: {
                show: true,
                position: 'top',
                color: '#333',
                fontWeight: 'bold',
                fontSize: 12,
                formatter: function(params) {
                    return formatVND(params.value.value);
                }
            },
            itemStyle: {
                color: '#36A2EB'
            }
        }],
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        }
    });

    // --- Chart: Top Shops ---
    const topShopsCtx = document.getElementById('topShopsChart').getContext('2d');
    new Chart(topShopsCtx, {
        type: 'bar',
        data: {
            labels: @json($shopNamesTop),
            datasets: [
                {
                    label: 'Số đơn hàng',
                    data: @json($topOrderCounts),
                    backgroundColor: '#FF6384',
                    yAxisID: 'y',
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return value.toLocaleString('vi-VN');
                        }
                    }
                },
                {
                    label: 'Doanh thu (VND)',
                    data: @json($topRevenues),
                    backgroundColor: '#36A2EB',
                    yAxisID: 'y1',
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#333',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return formatVND(value);
                        }
                    }
                }
            ]
        },
        options: {
            scales: {
                y: { beginAtZero: true },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    ticks: {
                        callback: function(value) {
                            return formatVND(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // --- Chart: Contracts by Admin (bar + line %) ---
    const contractsCtx = document.getElementById('contractsByAdminChart').getContext('2d');

    new Chart(contractsCtx, {
      data: {
        labels: @json($adminNames),
        datasets: [
          {
            type: 'bar',
            label: 'Số hợp đồng',
            data: @json($contractCounts),
            yAxisID: 'y',
            backgroundColor: '#42a5f5',
            order: 2,
            datalabels: {
              anchor: 'end',
              align: 'end',
              color: '#333',
              font: { weight: 'bold', size: 12 },
              formatter: (val) => val
            }
          },
          {
            type: 'line',
            label: 'Tỷ trọng (%)',
            data: @json($adminPercents),
            yAxisID: 'y1',
            borderColor: '#ffffff',          // line màu trắng
            borderDash: [6, 6],              // nét đứt
            borderWidth: 2,
            tension: 0,                      // line cứng
            fill: false,
            pointRadius: 3,
            pointBackgroundColor: '#ffffff', // chấm trắng
            pointBorderColor: '#0d47a1',     // viền chấm xanh đậm
            datalabels: {
              anchor: 'end',
              align: 'top',
              color: '#0d47a1',              // chữ xanh đậm
              font: { weight: 'bold', size: 11 },
              formatter: (val) => val + '%'
            }
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          datalabels: { display: true },
          legend: { display: true, position: 'top' },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const ds = ctx.dataset;
                if (ds.yAxisID === 'y') return `${ds.label}: ${ctx.raw}`;
                return `${ds.label}: ${ctx.raw}%`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Số hợp đồng' }
          },
          y1: {
            beginAtZero: true,
            max: 100,
            position: 'right',
            grid: { drawOnChartArea: false },
            title: { display: true, text: 'Tỷ trọng (%)' },
            ticks: { callback: (v) => v + '%' }
          },
          x: {
            ticks: { autoSkip: false, maxRotation: 35, minRotation: 0 }
          }
        }
      }
    });

    const revAdminCtx = document.getElementById('revenueByAdminChart').getContext('2d');

    new Chart(revAdminCtx, {
      type: 'bar',
      data: {
        labels: @json($adminRevenueNames),
        datasets: [{
          label: 'Doanh thu (VND)',
          data: @json($adminRevenueValues),
          backgroundColor: '#2196f3',
          datalabels: {
            anchor: 'end',
            align: 'end',
            color: '#333',
            font: { weight: 'bold', size: 12 },
            formatter: (value, ctx) => {
              const percent = @json($adminRevenuePercents)[ctx.dataIndex];
              // xuống dòng bằng \n
              return value.toLocaleString('vi-VN') + ' ₫\n' + '(' + percent + '%)';
            }
          }
        }]
      },
      options: {
        indexAxis: 'y', // chart ngang
        responsive: true,
        plugins: {
          datalabels: { display: true },
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const percent = @json($adminRevenuePercents)[ctx.dataIndex];
                return `${ctx.dataset.label}: ${ctx.raw.toLocaleString('vi-VN')} ₫ (${percent}%)`;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            title: { display: true, text: 'Doanh thu (VND)' },
            ticks: {
              callback: (v) => v.toLocaleString('vi-VN') + ' ₫'
            },
                 suggestedMax: Math.max(...@json($adminRevenueValues)) * 1.1 // +10% để có khoảng trống
          },
          y: { ticks: { autoSkip: false } }
        }
      }
    });

</script>
@endpush
