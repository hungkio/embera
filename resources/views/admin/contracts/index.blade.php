@extends('admin.layouts.master')

@section('title', __('Hợp đồng'))

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render('admin.contracts.index') }}
    </x-page-header>
@stop
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endpush

@section('page-content')

    @can('contracts.create')
    @endcan
    <form method="GET" action="{{ route('admin.contracts.index') }}" class="row g-3 mb-4 align-items-end filters">
        <div class="col-md-6">
            <label for="date_range">Khoảng ngày ký HĐ <span class="text-danger">*</span></label>
            <input type="text" name="date_range" id="date_range" class="form-control"
                   value="{{ request('date_range') }}" required autocomplete="off"/>
        </div>
        <div class="col-md-12 text-end mt-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel-fill"></i> Lọc dữ liệu
            </button>
        </div>
    </form>
<x-card title="Cảnh báo HĐ sắp hết hạn (30 ngày)">
    <div class="table-responsive" style="max-width:700px; margin:auto;">
        <table class="table table-sm table-bordered text-center align-middle" style="width:auto; margin:auto; font-size:14px;">
            <thead class="table-light">
                <tr>
                    <th class="text-start px-3">Nhân viên BD</th>
                    <th>Số HĐ sắp hết hạn</th>
                    <th>Tỷ lệ (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expiringByAdmin as $row)
                    <tr>
                        <td class="text-start px-3">{{ $row['admin_name'] }}</td>
                        <td>{{ $row['expiring_count'] }}</td>
                        <td>
                            <span class="badge
                                {{ $row['percent'] >= 50 ? 'bg-success' : ($row['percent'] >= 20 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $row['percent'] }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Không có HĐ nào sắp hết hạn</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td class="text-start px-3">Tổng</td>
                    <td>{{ $totalExpiring }}</td>
                    <td>{{ $totalExpiring > 0 ? '100%' : '0%' }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-card>

<x-card title="Hợp Đồng">
    <div class="table-responsive">
        {!! $dataTable->table([
        'class' => 'table table-striped table-bordered nowrap',
        'style' => 'width:100%',
        ], true) !!}
    </div>
</x-card>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" method="POST" action="{{ route('admin.contracts.import') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header  text-center">
                    <h4 class="modal-title" id="exampleModalLabel">Nhập HĐ</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-lg-right" for="redo">
                            File excel:
                        </label>
                        <div class="col-lg-9">
                            <input type="file" name="file" id="file"
                                   accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{$dataTable->scripts()}}
    <script>
        $(function () {
            $('.import').click(function () {
                $('#exampleModal').modal('show')
            })

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
        })
    </script>
@endpush
