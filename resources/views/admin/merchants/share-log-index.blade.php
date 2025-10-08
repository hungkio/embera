@extends('admin.layouts.master')

@section('title', __('Chi tiết chia sẻ doanh thu'))

@section('page-header')
<x-page-header>
    {{ Breadcrumbs::render('admin.share-logs.detail', $log) }}
</x-page-header>
@stop

@section('page-content')

<x-card title="Chi tiết chia sẻ doanh thu">
    <!-- Thanh nút -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="fal fa-file-invoice-dollar mr-2"></i>
            Chi tiết chia sẻ doanh thu
        </h5>
        <div>
            <a href="{{ route('admin.merchants.share-logs') }}" class="btn btn-secondary me-2">
                <i class="fal fa-arrow-left mr-1"></i> Quay lại
            </a>
            <a href="{{ route('admin.merchant-share-logs.export', ['id' => $log->id]) }}" class="btn btn-success me-2">
                <i class="fal fa-file-excel mr-1"></i> Xuất Excel
            </a>
            <button class="btn btn-primary me-2" onclick="window.print()">
                <i class="fal fa-print mr-1"></i> In
            </button>
            <button class="btn btn-warning" onclick="location.reload()">
                <i class="fal fa-undo mr-1"></i> Thiết lập lại
            </button>
        </div>
    </div>

    <!-- Thông tin log -->
    <div class="row mb-4">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th>Merchant</th>
                    <td>{{ $log->merchant->username ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Mã hợp đồng</th>
                    <td>{{ $log->contract_no }}</td>
                </tr>
                <tr>
                    <th>Khách hàng</th>
                    <td>{{ $log->customer_name }}</td>
                </tr>
                <tr>
                    <th>Kỳ chia sẻ</th>
                    <td>{{ str_pad($log->month, 2, '0', STR_PAD_LEFT) }}/{{ $log->year }}</td>
                </tr>
                <tr>
                    <th>Ngày ghi log</th>
                    <td>{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <div class="col-md-6">
            <table class="table table-bordered">
                <tr>
                    <th>Tổng doanh thu</th>
                    <td>{{ number_format($log->total, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <th>Tỷ lệ chia sẻ</th>
                    <td>
                        @if($log->share_type === 'fixed')
                            {{ number_format($log->share_percent, 0, ',', '.') }} ₫ (Cố định)
                        @else
                            {{ $log->share_percent > 1 ? $log->share_percent : $log->share_percent * 100 }}% (Phần trăm)
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Số tiền chia sẻ</th>
                    <td class="text-success fw-bold">{{ number_format($log->share_money, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <th>Số đơn hàng</th>
                    <td>{{ $log->number_of_order }}</td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        @if($log->status === 'completed')
                            <span class="badge bg-success">Hoàn tất</span>
                        @elseif($log->status === 'pending')
                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bảng chi tiết chia sẻ -->
    <h6 class="mt-4 mb-3"><i class="fal fa-list-alt mr-2"></i> Chi tiết chia sẻ theo cửa hàng</h6>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Tên cửa hàng</th>
                    <th>Doanh thu</th>
                    <th>Chia sẻ</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['shop_data'] ?? [] as $i => $shop)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $shop['ten_cua_hang'] ?? '-' }}</td>
                        <td>{{ $shop['doanh_thu'] ?? '0 VNĐ' }}</td>
                        <td>{{ $shop['chia_se'] ?? '0%' }}</td>
                        <td>{{ $shop['thanh_toan'] ?? '0 VNĐ' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-card>

@stop

@push('css')
<style>
    th { width: 40%; background: #f9f9f9; }
    td { vertical-align: middle; }
</style>
@endpush
