{{-- resources/views/admin/merchants/share-log-detail.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Chi tiết lịch sử chia sẻ')
@section('page-header')
{{ Breadcrumbs::render('admin.merchants-history.detail', $log) }}
@stop

@section('page-content')
<x-card title="Chi tiết lịch sử chia sẻ">
    {{--Thông tin chung--}}
    <div class="row mb-4">
        <div class="col-md-3"><strong>Kỳ:</strong> {{ str_pad($log->month, 2, '0', STR_PAD_LEFT)
            }}/{{ $log->year }}
        </div>
        <div class="col-md-3"><strong>Loại chia sẻ:</strong>
            {{ $log->share_type === 'fixed' ? 'Số đơn' : 'Phần trăm' }}
        </div>
        <div class="col-md-3"><strong>Tổng doanh thu:</strong>
            {{ $log->share_type === 'fixed' ? '-' : number_format($log->total, 0, ',', '.'). ' VNĐ'
            }}
        </div>
        <div class="col-md-3"><strong>Tổng tiền chia sẻ:</strong>
            {{ number_format($log->share_money, 0, ',', '.'). ' VNĐ' }}
        </div>
        <div class="col-md-3 mt-2"><strong>Số đơn hàng:</strong> {{ $log->number_of_order }}</div>
        @if($log->share_type==='percentage')
        <div class="col-md-3 mt-2"><strong>Tỷ lệ chia sẻ:</strong>
            {{ number_format($log->share_percent). '%' }}
        </div>
        @endif
        <div class="col-md-3 mt-2"><strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse(
                $log->date)->format('d/m/Y') }}
        </div>
        {{--XÓA phần Người tạo--}}
    </div>

    {{--Bảng Chi tiết theo shop--}}
    <h5>Chi tiết từng shop</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th>STT</th>
                <th>Tên shop</th>
                <th>Địa chỉ</th>
                <th>Số đơn hàng</th>
                <th>Chia sẻ</th>
                <th>Thanh toán</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data['shop_data'] as $shop)
            <tr>
                <td>{{ $shop['stt'] }}</td>
                <td>{{ $shop['shop_name'] }}</td>
                <td>{{ $shop['dia_chi_shop'] }}</td>
                <td>{{ $shop['doanh_thu'] }}</td>
                <td>{{ $shop['chia_se'] }}</td>
                <td>{{ $shop['thanh_toan'] }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="font-weight-bold">
                <td colspan="5" class="text-right">Tổng số tiền thanh toán:</td>
                <td>{{ $data['tong_thanh_toan_share'] }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</x-card>
@stop
