<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
<p>Hợp đồng số: <strong>{{ $hop_dong_so }}</strong></p>
<p>Bên B: <strong>{{ $ben_b }}</strong></p>

<table>
    <thead>
    <tr>
        <th>STT</th>
        <th>Tên shop</th>
        <th>Địa chỉ</th>
        <th>Doanh thu</th>
        <th>Tỷ lệ chia sẻ</th>
        <th>Thanh toán</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($shop_data as $shop)
    <tr>
        <td>{{ $shop['stt'] }}</td>
        <td>{{ $shop['shop_name'] }}</td>
        <td>{{ $shop['dia_chi_shop'] }}</td>
        <td>{{ $shop['doanh_thu'] }}</td>
        <td>{{ $shop['chia_se'] }}%</td>
        <td>{{ $shop['thanh_toan'] }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

<p><strong>Tổng thanh toán:</strong> {{ $tong_thanh_toan }}</p>
<p><strong>Bằng chữ:</strong> {{ $tong_thanh_toan_text }}</p>
</body>
</html>
