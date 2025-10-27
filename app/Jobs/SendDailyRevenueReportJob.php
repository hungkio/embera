<?php

namespace App\Jobs;

use App\DataTables\Export\OrderExportHandler;
use App\Mail\DailyRevenueReportMail;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendDailyRevenueReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            $tz     = 'Asia/Ho_Chi_Minh';
            $dateVN = Carbon::yesterday($tz);
            $from   = $dateVN->copy()->startOfDay();
            $to     = $dateVN->copy()->endOfDay();

            $orders = Order::query()
                ->leftJoin('shops', 'shops.shop_name', '=', 'orders.rental_shop')
                ->select('orders.*', 'shops.share_rate_type', 'shops.share_rate')
                ->whereBetween('orders.return_time', [$from, $to])
                ->where('orders.order_amount', '>', 0)
                ->orderByDesc('orders.return_time')
                ->get();

            Log::info('📦 Báo cáo doanh thu', [
                'from'  => $from->format('Y-m-d H:i:s'),
                'to'    => $to->format('Y-m-d H:i:s'),
                'count' => $orders->count(),
            ]);

            if ($orders->isEmpty()) {
                Log::info("❗ Không có giao dịch nào ngày {$dateVN->format('d/m/Y')}");
                return;
            }

            // Tạo thư mục nếu chưa có
            $reportsDir = storage_path('app/reports');
            if (!is_dir($reportsDir)) {
                mkdir($reportsDir, 0755, true);
            }

            $filename     = 'Bao_cao_doanh_thu_' . $dateVN->format('Y_m_d') . '.xlsx';
            $relativePath = 'reports/' . $filename;

            // ✅ Lưu file Excel vào disk local
            Excel::store(new OrderExportHandler($orders), $relativePath, 'local');

            // Kiểm tra file có tồn tại
            $absolutePath = storage_path('app/' . $relativePath);
            Log::info('📁 File Excel đã tạo', [
                'path' => $absolutePath,
                'exists' => file_exists($absolutePath),
                'size' => file_exists($absolutePath) ? filesize($absolutePath) : 0,
            ]);

            // Gửi mail đính kèm
            Mail::to('nhanmt@embera.vn')->send(
                new DailyRevenueReportMail($absolutePath, $dateVN)
            );

            Log::info("✅ Đã gửi báo cáo doanh thu ngày {$dateVN->format('d/m/Y')} tới nhanmt@embera.vn");
        } catch (\Throwable $e) {
            Log::error('🔥 Lỗi gửi báo cáo doanh thu: ' . $e->getMessage());
        }
    }
}
