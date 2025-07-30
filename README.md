# Page
Hướng dẫn chạy script trên hosting mắt bão
Kính chào Quý khách,

Liên quan đến vấn đề Quý khách phản ánh, bộ phận kỹ thuật đã tiến hành thực thi lệnh tạo thư mục của Laravel như sau trên terminal:

 
 
/opt/plesk/php/8.2/bin/php artisan storage:link
Đồng thời, cấu hình cronjob đã được thiết lập với nội dung:

 
 
/opt/plesk/php/8.2/bin/php /var/www/vhosts/embera.vn/embera.tech/artisan schedule:run >> /dev/null 2>&1
Kính mong Quý khách kiểm tra lại và liên hệ nếu cần hỗ trợ thêm.

Trân trọng cảm ơn Quý khách!
