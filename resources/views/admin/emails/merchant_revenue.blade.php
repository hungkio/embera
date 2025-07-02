<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: 900px;
            margin: 20px auto;
            border: 2px solid #000;
            padding: 25px;
            box-sizing: border-box;
        }
        .header, .title, .footer, .signature {
            width: 100%;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .header .company {
            font-weight: bold;
            font-size: 16px;
        }
        .header .bm {
            font-style: italic;
        }
        .header .address {
            margin-top: 5px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin: 30px 0 20px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 10px 0;
        }
        .subtitle {
            display: inline-block;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row td {
            font-weight: bold;
        }
        .footer {
            font-size: 13px;
            line-height: 1.6;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        .signature {
            margin-top: 30px;
        }
    </style>
    <title></title>
</head>
<body>
<div class="container">

    <div class="header">
        <div class="company">CÔNG TY CỔ PHẦN CÔNG NGHỆ EMBERA</div>
        <div class="bm">BM: ....</div>
        <div class="address">Số 166 Phạm Văn Đồng, Phường Xuân Đỉnh, Quận Bắc Từ Liêm, Thành phố Hà Nội, Việt Nam</div>
    </div>

    <div class="title">
        CỘNG HOÀ XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
        <div class="subtitle">Độc lập – Tự do – Hạnh phúc</div><br><br>
        BIÊN BẢN XÁC NHẬN DOANH THU CHIA SẺ
    </div>


    <div class="section">
        Hôm nay, ngày {{ $content['hom_nay_ngay'] ?? '' }} tháng {{ $content['hom_nay_thang'] ?? '' }} năm {{ $content['hom_nay_nam'] ?? '' }}, chúng tôi gồm có:<br>
        <strong>BÊN A: CÔNG TY CỔ PHẦN CÔNG NGHỆ EMBERA</strong><br>
        <strong>BÊN B: {{ $content['ben_b'] ?? '' }}</strong><br><br>
        Căn cứ theo Hợp đồng số {{ $content['hop_dong_so'] ?? '' }} ký giữa Bên A và Bên B, hai bên cùng thống nhất lập biên bản xác nhận doanh thu chia sẻ như sau:
    </div>

    <div class="section">
        <strong>· Kỳ xác nhận:</strong> Từ ngày {{ $content['from_day'] ?? '' }} tháng {{ $content['from_month'] ?? '' }} năm {{ $content['from_year'] ?? '' }} đến ngày {{ $content['to_day'] ?? '' }} tháng {{ $content['to_month'] ?? '' }} năm {{ $content['to_year'] ?? '' }}
    </div>

    <div class="section">
        <strong>· Chi tiết doanh thu chia sẻ theo từng điểm lắp đặt:</strong>
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Tên điểm</th>
                <th>Địa chỉ</th>
                <th>Doanh thu (VNĐ)</th>
                <th>Tỷ lệ chia sẻ (%)</th>
                <th>Số tiền thanh toán (VNĐ)</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($content['shop_data'] ?? [] as $shop)
            <tr>
                <td>{{ $shop['stt'] }}</td>
                <td>{{ $shop['shop_name'] }}</td>
                <td>{{ $shop['dia_chi_shop'] }}</td>
                <td>{{ $shop['doanh_thu'] }}</td>
                <td>{{ $shop['chia_se'] }}</td>
                <td>{{ $shop['thanh_toan'] }} VNĐ</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" style="text-align: right; font-weight: bold;">Tổng</td>
                <td style="font-weight: bold;">{{ $content['tong_thanh_toan'] ?? '' }}</td>
            </tr>
            </tbody>
        </table>
    </div>


    <div class="section">
        <strong>· Tổng số tiền chia sẻ doanh thu Bên A thanh toán cho Bên B:</strong> {{ $content['tong_thanh_toan'] ?? '' }} ({{ $content['tong_thanh_toan_text'] ?? '' }} (Bằng chữ))<br>
        <strong>· Hình thức thanh toán:</strong> chuyển khoản vào tài khoản BÊN B:<br>
        - Tên chủ tài khoản: {{ $content['chu_tai_khoan'] ?? '' }}<br>
        - Số tài khoản: {{ $content['so_tai_khoan'] ?? '' }}<br>
        - Ngân hàng: {{ $content['ten_ngan_hang'] ?? '' }}
    </div>

    <div class="footer">
        Biên bản này được gửi qua email và có hiệu lực như một biên bản xác nhận chính thức. Biên bản được lưu trữ cùng thông tin gửi email để làm căn cứ xác nhận giữa hai bên.<br>
        Trong vòng 05 (năm) ngày kể từ ngày biên bản này được gửi, nếu Bên B không có phản hồi chính thức bằng văn bản hoặc email, thì được hiểu là Bên B đã đồng ý toàn bộ nội dung biên bản cũng như các điều khoản thanh toán nêu trên.
    </div>

    <div class="signature section">
        <strong>Người lập biên bản:</strong><br>
        Họ và tên: {{ $content['giam_doc_ky'] ?? '' }}<br>
        Chức vụ: {{ $content['chuc_vu'] ?? '' }}<br>
        SĐT: {{ $content['so_dien_thoai'] ?? '' }}<br>
        Email: {{ $content['email'] ?? '' }}
    </div>

</div>
</body>
</html>
