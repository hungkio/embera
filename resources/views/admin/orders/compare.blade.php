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

    </style>
@endpush

@section('page-content')
    <h3 class="mb-4">Báo cáo đối soát giao dịch</h3>

    <form method="GET" action="{{ route('admin.mergeTransaction') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-6">
            <label for="date_range">Khoảng ngày thuê <span class="text-danger">*</span></label>
            <input type="text" name="date_range" id="date_range" class="form-control"
                   value="{{ request('date_range') }}" required autocomplete="off"/>
        </div>
        <div class="col-md-3">
            <label for="code">Mã giao dịch</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ request('code') }}">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Lọc</button>
            <button class="btn btn-warning" type="button" id="btn-reconcile"><i class="fas fa-exchange-alt"></i> Đối
                soát giao dịch
            </button>
        </div>
    </form>

    <form action="{{ route('admin.mb-transactions.import') }}" method="POST" enctype="multipart/form-data"
          class="row g-3 mb-4 align-items-end">
        @csrf
        <div class="col-md-4">
            <label for="input_file_in">File giao dịch vào:</label>
            <input type="file" name="input_file_in" id="input_file_in" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="input_file_out">File giao dịch ra:</label>
            <input type="file" name="input_file_out" id="input_file_out" class="form-control" required>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import file</button>
        </div>
    </form>

    <x-card id="datatable-section">
        {{$dataTable->table()}}
    </x-card>

    <x-card id="reconciliation-report" class="d-none mt-4">
        <h5>Kết quả đối soát giao dịch</h5>
        <table id="reconcile-table" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Mã giao dịch</th>
                <th>Thời gian thanh toán đơn hàng</th>
                <th>Thời gian thanh toán MB</th>
                <th>FT In</th>
                <th>FT Out</th>
                <th>Số tiền đơn hàng</th>
                <th>Số tiền MB</th>
                <th>Trạng thái</th>
                <th>Lý do</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </x-card>

@endsection


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

{{--    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>--}}
    {{$dataTable->scripts()}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script>
        $(document).ready(function () {
            let start = moment("{{ request('date_from') ?? now()->startOfMonth()->format('Y-m-d') }}");
            let end = moment("{{ request('date_to') ?? now()->endOfMonth()->format('Y-m-d') }}");

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

            $('#btn-reconcile').click(function () {
                let dateRange = $('#date_range').val();
                let code = $('#code').val();

                $('#datatable-section').hide();
                $('#reconciliation-report').removeClass('d-none');

                // Nếu đã có table thì destroy trước để reload
                if ($.fn.DataTable.isDataTable('#reconcile-table')) {
                    $('#reconcile-table').DataTable().destroy();
                }

                $('#reconcile-table').DataTable({
                    processing: true,
                    serverSide: false,
                    paging: true,
                    pageLength: 25,
                    buttons: [
                        // 'copy', 'csv', 'pdf', 'print',
                        'excel',
                    ],
                    dom: 'Bfrtip',
                    language: {
                        search: "Tìm kiếm:",
                        lengthMenu: "Hiển thị _MENU_ dòng",
                        info: "Hiển thị _START_ đến _END_ trên tổng _TOTAL_ dòng",
                        paginate: {
                            first: "Đầu",
                            last: "Cuối",
                            next: "→",
                            previous: "←"
                        },
                        emptyTable: "Không có dữ liệu",
                    },
                    ajax: {
                        url: "{{ route('admin.compare') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            date_range: dateRange,
                            order_code: code,
                        },
                        dataSrc: 'data' // quan trọng: map đúng với key response
                    },
                    columns: [
                        { data: 'code' },
                        { data: 'payment_time', defaultContent: '' },
                        { data: 'date_in', defaultContent: '' },
                        { data: 'ft_in', defaultContent: '' },
                        { data: 'ft_out', defaultContent: '' },
                        { data: 'order_amount', defaultContent: ''},
                        { data: 'revenue', defaultContent: ''},
                        {
                            data: 'matched',
                            render: matched => matched
                                ? '<span class="badge bg-success">Khớp</span>'
                                : '<span class="badge bg-danger">Không khớp</span>'
                        },
                        { data: 'reason' },
                    ],
                });
            });

        });

    </script>
@endpush
