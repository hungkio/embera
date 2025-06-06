@extends('admin.layouts.master')

@section('title', __('Orders'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        @media (max-width: 767.98px) {
            .btn-danger {
                margin-left: 0rem !important;
            }
        }

        @media (width: 320px) {
            .btn-danger {
                margin-left: .625rem !important;
            }
        }
    </style>
@endpush

@section('page-content')
    @include('admin.orders._filters', [
            'staffList' => $staffList,
            'shopTypeList' => $shopTypeList,
            'shopNameList' => $shopNameList,
            'regionList' => $regionList,
            'cityList' => $cityList,
            'areaList' => $areaList,
            'filters' => $filters,
        ])
    @if(request()->filled('date_from') && request()->filled('date_to'))
        <div class="row mb-4">

            <!-- Tổng doanh thu -->
            <div class="col-md-12 mb-3">
                <div class="alert alert-info">
                    <strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND
                </div>
            </div>

            <!-- Bảng theo ngày -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        📅 Doanh thu theo ngày
                        <button class="btn btn-sm btn-success float-right" onclick="exportTableToExcel('date-table', 'DoanhThu_TheoNgay')">Xuất doanh thu theo ngày</button>
                    </div>
                    <div class="card-body p-2">
                        <div class="scroll-box table-responsive" style="max-height: 800px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" id="date-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ngày</th>
                                    <th>Số đơn</th>
                                    <th>Doanh thu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($byDate as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['count'] }}</td>
                                        <td>{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng theo shop -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        🏪 Doanh thu theo cửa hàng
                        <button class="btn btn-sm btn-success float-right" onclick="exportTableToExcel('shop-table', 'DoanhThu_Shop')">
                            Xuất doanh thu theo cửa hàng
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <div class="scroll-box table-responsive" style="max-height: 800px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" id="shop-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Shop</th>
                                    <th>Doanh thu</th>
                                    <th>% Chia sẻ</th>
                                    <th>Doanh thu chia sẻ</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($byShop as $i => $shop)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $shop['shop'] }}</td>
                                        <td>{{ number_format($shop['revenue'], 0, ',', '.') }}</td>
                                        <td>{{ $shop['sharing_percent'] }}</td>
                                        <td>{{ number_format($shop['revenue']*$shop['sharing_percent']/100, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng theo nhân viên -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        👤 Doanh thu theo nhân viên
                        <button class="btn btn-sm btn-success float-right" onclick="exportTableToExcel('staff-table', 'DoanhThu_NhanVien')">
                            Xuất doanh thu theo nhân viên
                        </button>
                    </div>
                    <div class="card-body p-2">
                        <div class="scroll-box table-responsive" style="max-height: 800px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mb-0" id="staff-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nhân viên</th>
                                    <th>Doanh thu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($byStaff as $i => $staff)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $staff['employee'] }}</td>
                                        <td>{{ number_format($staff['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách đơn hàng</h3>
        <form action="{{ route('admin.orders.import') }}" method="POST" enctype="multipart/form-data"
              class="d-inline-block me-3">
            @csrf
            <input type="file" name="import_file" accept=".xlsx,.xls" required
                   class="form-control d-inline-block w-auto" style="display:inline-block;">
            <button type="submit" class="btn btn-success">Import Orders Excel</button>
        </form>
    </div>
    <x-card>
        {{$dataTable->table()}}
    </x-card>

@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    {{$dataTable->scripts()}}
    <script>
        function exportTableToExcel(tableId, filename = 'export') {
            const table = document.getElementById(tableId);
            const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
            XLSX.writeFile(wb, filename + ".xlsx");
        }

        $(document).ready(function () {
            $('a[data-toggle="tooltip"]').tooltip();

            let start = moment("{{ request('date_from') ?? now()->startOfMonth()->format('YYYY-MM-DD') }}");
            let end = moment("{{ request('date_to') ?? now()->endOfMonth()->format('YYYY-MM-DD') }}");

            $('#date_range').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: "Áp dụng",
                    cancelLabel: "Hủy",
                    fromLabel: "Từ",
                    toLabel: "Đến",
                    customRangeLabel: "Tùy chọn",
                    daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                    monthNames: ["Th1", "Th2", "Th3", "Th4", "Th5", "Th6", "Th7", "Th8", "Th9", "Th10", "Th11", "Th12"],
                    firstDay: 1
                }
            });

            $('.filters .select2').select2({
                // width: '100%',
            });
        });
        $(document).on('change', '#select_status', function () {
            var status = $(this).val();
            var url = $(this).attr('data-url');
            confirmAction('Bạn có muốn thay đổi trạng thái ?', function (result) {
                if (result) {
                    $.ajax({
                        url: url,
                        data: {
                            'status': status
                        },
                        type: 'POST',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == true) {
                                showMessage('success', res.message);
                            } else {
                                showMessage('error', res.message);
                            }
                            window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                        },
                    });
                } else {
                    window.LaravelDataTables['{{ $dataTable->getTableAttribute('id') }}'].ajax.reload();
                }
            });
        });
    </script>
@endpush
