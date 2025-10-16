<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo doanh thu ngày {{ $date->format('d/m/Y') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333;">Báo cáo doanh thu ngày {{ $date->format('d/m/Y') }}</h2>

        <p>Xin chào,</p>
        <p>Đính kèm là <strong>báo cáo doanh thu ngày {{ $date->format('d/m/Y') }}</strong>.</p>

        <p>Vui lòng mở file Excel trong mail này để xem chi tiết từng đơn hàng.</p>

        <hr>
        <p style="font-size: 13px; color: #666;">
            Email này được gửi tự động từ hệ thống Embera Tech.<br>
            Vui lòng không trả lời email này.
        </p>
    </div>
</body>
</html>
