
<h3>BẢNG KÊ CHI TIẾT GIAO DỊCH THUÊ PIN NGÀY {{ date('d') }} THÁNG {{ date('m') }} NĂM {{ date('Y') }}</h3>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>STT</th>
        <th>ID máy</th>
        <th>Thời gian thuê</th>
        <th>Thời gian trả</th>
        <th>Địa điểm cho thuê</th>
        <th>Thời gian sử dụng</th>
        <th>Loại tiền</th>
        <th>Doanh thu</th>
    </tr>
    </thead>
    <tbody>
    @php($total = 0)
    @foreach($orders as $stt =>  $row)
        @if($row->order_amount > 0)
            <tr>
                <td>{{ $stt+1 }}</td>
                <td>{{ $row->rental_equipment_id }}</td>
                <td>{{ formatDate($row->rental_time) }}</td>
                <td>{{ formatDate($row->return_time) }}</td>
                <td>{{ $row->rental_shop }}</td>
                <td>{{ $row->duration_of_use }}</td>
                <td>{{ $row->currency }}</td>
                <td>{{ number_format($row->order_amount, 0, '.', ',') }}</td>
                @php($total += $row->order_amount)
            </tr>
        @endif
    @endforeach
    <tr>
        <td></td>
        <td>Tổng</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $row->currency }}</td>
        <td>{{ number_format($total, 0, '.', ',') }}</td>
    </tr>
    </tbody>
</table>
