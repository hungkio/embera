@extends('admin.layouts.master')
@section('title', 'Chi tiết chia sẻ doanh thu')

@section('page-content')
<div class="card p-4 shadow-sm">
    <h4 class="mb-3 text-primary">Chi tiết chia sẻ doanh thu</h4>
    <table class="table table-bordered">
        <tr><th>Merchant</th><td>{{ $log->merchant->username ?? '-' }}</td></tr>
        <tr><th>Khách hàng</th><td>{{ $log->customer_name ?? '-' }}</td></tr>
        <tr><th>Mã HĐ</th><td>{{ $log->contract_no ?? '-' }}</td></tr>
        <tr><th>Kỳ</th><td>{{ str_pad($log->month, 2, '0', STR_PAD_LEFT) }}/{{ $log->year }}</td></tr>
        <tr><th>Tổng thu nhập</th><td>{{ number_format($log->total, 0, ',', '.') }} VNĐ</td></tr>
        <tr><th>Số đơn</th><td>{{ $log->number_of_order ?? 0 }}</td></tr>
        <tr><th>Tỷ lệ chia sẻ</th><td>{{ $log->share_percent }} {{ $log->share_type === 'fixed' ? 'VNĐ' : '%' }}</td></tr>
        <tr><th>Số tiền chia sẻ</th><td>{{ number_format($log->share_money, 0, ',', '.') }} VNĐ</td></tr>
        <tr><th>Loại chia sẻ</th><td>{{ ucfirst($log->type) }}</td></tr>
        <tr><th>Trạng thái</th><td>{{ ucfirst($log->status ?? '-') }}</td></tr>
        <tr><th>Ngày tạo</th><td>{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td></tr>
    </table>
    <div class="text-center mt-3">
        <a href="{{ route('admin.share-logs.index') }}" class="btn btn-light">
            <i class="fal fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>
</div>
@endsection
