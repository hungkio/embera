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
'merchantList' => $merchantList,
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


    <ul class="nav nav-tabs mb-0" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">📅 Doanh thu theo ngày</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">🏪 Doanh thu theo cửa hàng</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">👤 Doanh thu theo nhân viên</a>
        </li>
    </ul>
    <div class="tab-content w-100" id="myTabContent">
        <div class="tab-pane card fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <button class="btn btn-sm m-2 btn-success float-right"
                    onclick="exportTableToExcel('date-table', 'DoanhThu_TheoNgay')">Xuất doanh thu theo ngày
            </button>
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
                            <td>{{ number_format($row['revenue'], 0, '.', ',') }}đ</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane card fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <button class="btn btn-sm m-2 btn-success float-right"
                    onclick="exportTableToExcel('shop-table', 'DoanhThu_Shop')">
                Xuất doanh thu theo cửa hàng
            </button>
            <div class="card-body p-2">
                <div class="scroll-box table-responsive" style="max-height: 800px; overflow-y: auto;">
                    <table class="table table-bordered table-sm mb-0" id="shop-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Shop</th>
                            <th>Địa chỉ</th>
                            <th>Doanh thu (VND)</th>
                            <th>Số đơn hàng</th>
                            <th>Tỷ lệ chia sẻ</th>
                            <th>Số tiền thanh toán (VND)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($byShop as $i => $shop)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $shop['shop'] }}</td>
                            <td>{{ $shop['address'] }}</td>
                            <td>{{ number_format($shop['revenue'], 0, '.', ',') }} đ</td>
                            <td>{{ $shop['number_of_order'] }}</td>
                            <td>{{ $shop['sharing_percent'] }}</td>
                            <td>{{ number_format($shop['sharing_revenue'], 0, '.', ',') }}đ</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane card fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <button class="btn btn-sm m-2 btn-success float-right"
                    onclick="exportTableToExcel('staff-table', 'DoanhThu_NhanVien')">
                Xuất doanh thu theo nhân viên
            </button>
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
                            <td>{{ number_format($staff['revenue'], 0, ',', ',') }}</td>
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

<div class="modal fade" id="sendEmailModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <form id="formSendEmail">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Send Email</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <div class="modal-body">
          
          <!-- Email -->
          <div class="form-group">
            <label>Email nhận <span class="text-danger">*</span></label>
            <input type="email" name="email" value="goatn4b1@gmail.com" class="form-control" required>
          </div>

          <!-- Title -->
          <div class="form-group">
            <label>Tiêu đề <span class="text-danger">*</span></label>
            <input type="text" name="title" value="BC doanh thu {{ date('d/m/Y') }}" class="form-control" required>
          </div>

          <!-- Content -->
          <div class="form-group">
            <label>Nội dung (nếu cần)</label>
            <textarea name="content" class="form-control" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label>Dữ liệu đơn hàng</label>
            <input type="file" name="original_data" class="form-control" accept=".xlsx,.xls,.csv">
          </div>

        </div>
        
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send</button>
        </div>
      </form>
      
    </div>
  </div>
</div>

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
                        window.LaravelDataTables['{{ $dataTable->getTableAttribute('
                        id
                        ') }}'
                    ].
                        ajax.reload();
                    },
                });
            } else {
                window.LaravelDataTables['{{ $dataTable->getTableAttribute('
                id
                ') }}'
            ].
                ajax.reload();
            }
        });
    });

    $(document).on('click', '.btn-send-email', function () {
        $('#sendEmailModal').modal('show');
    });

    $(document).on('submit', '#formSendEmail', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        $('.filters').serializeArray().forEach(function (field) {
            formData.append(field.name, field.value);
        });

        $.ajax({
            url: '/admin/orders/export/send-email',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                alert('Gửi email thành công!');
                $('#sendEmailModal').modal('hide');
                $('#formSendEmail')[0].reset();
            },
            error: function (err) {
                alert('Có lỗi xảy ra!');
            }
        });
    });
</script>
@endpush
