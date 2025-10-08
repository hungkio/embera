<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Merchant</th>
        <th>Mã HĐ</th>
        <th>Khách hàng</th>
        <th>Kỳ</th>
        <th>Tổng thu nhập</th>
        <th>Số đơn</th>
        <th>Share</th>
        <th>Số tiền share</th>
        <th>Loại chia sẻ</th>
        <th>Nguồn ghi</th>
        <th>Ngày ghi log</th>
        <th>Trạng thái</th>
        <th>Tác vụ</th>
    </tr>
    </thead>

    <tbody>
    @foreach($logs as $index => $log)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ optional($log->merchant)->username ?? '-' }}</td>
            <td>{{ $log->contract_no ?? '-' }}</td>
            <td>{{ $log->customer_name ?? '-' }}</td>
            <td>{{ str_pad($log->month, 2, '0', STR_PAD_LEFT) }}/{{ $log->year }}</td>

            {{-- Tổng thu nhập (định dạng tiền) --}}
            <td data-format="@">{{ number_format($log->total, 0, ',', '.') }}&nbsp;VNĐ</td>

            {{-- Số đơn (hiển thị thô) --}}
            <td>{{ $log->total ?? 0 }}</td>

            {{-- Share (phần trăm hoặc cố định) --}}
            <td>
                {{ $log->share_type === 'fixed'
                    ? number_format($log->share_percent, 0, ',', '.') . ' VNĐ'
                    : $log->share_percent . '%' }}
            </td>

            {{-- Số tiền share --}}
            <td data-format="@">{{ number_format($log->share_money, 0, ',', '.') }}&nbsp;VNĐ</td>

            {{-- Loại chia sẻ --}}
            <td>{{ $log->share_type === 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>

            {{-- Nguồn ghi (type) --}}
            <td>{{ ucfirst($log->type ?? '-') }}</td>

            {{-- Ngày ghi log --}}
            <td>{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td>

            {{-- Trạng thái --}}
            <td>{{ $log->status ?? '-' }}</td>

            {{-- Tác vụ: có thể để trống hoặc ghi “Xem chi tiết” --}}
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
