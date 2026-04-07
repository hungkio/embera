{{-- ===== TIÊU ĐỀ ===== --}}
<table width="100%">
    <tr><td colspan="8" style="height:10px;"></td></tr>

    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="text-align:center; font-weight:bold; font-size:16px;">
            @if($date)
                BẢNG KÊ CHI TIẾT GIAO DỊCH THUÊ PIN NGÀY {{ \Illuminate\Support\Carbon::parse($date)->format('d') }} THÁNG {{ \Illuminate\Support\Carbon::parse($date)->format('m') }} NĂM {{ \Illuminate\Support\Carbon::parse($date)->format('Y') }}
            @else
                BẢNG KÊ CHI TIẾT GIAO DỊCH THUÊ PIN NGÀY {{ date('d') }} THÁNG {{ date('m') }} NĂM {{ date('Y') }}
            @endif
        </td>
    </tr>

    <tr>
        <td colspan="3"></td>
        <td colspan="5" style="text-align:center; font-style:italic;">
            Kèm theo hóa đơn số ………… ngày ……/……/……/{{ date('Y') }}
        </td>
    </tr>

    <tr><td colspan="8" style="height:10px;"></td></tr>
</table>

{{-- ===== BẢNG DỮ LIỆU ===== --}}
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

        {{-- 2 dòng trống --}}
        <tr><td colspan="8" style="height:20px;"></td></tr>
        <tr><td colspan="8" style="height:20px;"></td></tr>

        {{-- Bộ phận xác nhận --}}
        <tr>
            <td colspan="4"></td>
            <td style="text-align:left; font-weight:bold; height:40px;">
                Bộ phận đối soát xác nhận
            </td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
