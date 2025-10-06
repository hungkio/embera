<h3>BẢNG KÊ CHI TIẾT GIAO DỊCH THUÊ PIN NGÀY {{ date('d') }} THÁNG {{ date('m') }} NĂM {{ date('Y') }}</h3>

<table border="1" cellpadding="5" cellspacing="0" width="100%">
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
        @foreach($orders as $stt => $row)
            @if($row->order_amount > 0)
                <tr>
                    <td>{{ $stt+1 }}</td>
                    <td>{{ $row->rental_equipment_id }}</td>
                    <td>{{ formatDate($row->rental_time) }}</td>
                    <td>{{ formatDate($row->return_time) }}</td>
                    <td>{{ $row->rental_shop }}</td>
                    <td>{{ $row->duration_of_use }}</td>
                    <td>{{ $row->currency }}</td>
                    <td>{{ $row->order_amount }}</td>
                    @php($total += $row->order_amount)
                </tr>
            @endif
        @endforeach

        {{-- Dòng tổng --}}
        <tr>
            <td></td>
            <td><strong>Tổng</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>{{ $row->currency ?? '' }}</strong></td>
            <td><strong>{{ number_format($total, 0, '.', ',') }}</strong></td>
        </tr>

        {{-- Thêm khoảng cách 2 dòng trống --}}
        <tr><td colspan="8" style="height:20px;"></td></tr>
        <tr><td colspan="8" style="height:20px;"></td></tr>

        {{-- Dòng Bộ phận đối soát xác nhận (nằm sau Tổng) --}}
        <tr>
            <td colspan="4"></td> {{-- 4 cột trống bên trái --}}
            <td style="text-align:left; font-weight:bold; height:40px;">
                Bộ phận đối soát xác nhận
            </td>
            <td colspan="3"></td> {{-- các cột còn lại trống --}}
        </tr>
    </tbody>
</table>
