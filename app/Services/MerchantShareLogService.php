<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\MerchantShareLog;
use Illuminate\Support\Facades\Log;

class MerchantShareLogService
{
    /**
     * Ghi log chia sẻ doanh thu (dùng chung cho email, zalo...)
     */
    public function logShare(Merchant $merchant, array $data, string $shareType, string $type, string $status = ''): void
    {
        $shopsData = collect($data['shop_data'] ?? []);

        // Parser tiền hiện tại của bạn (giữ nguyên)
        $parse = function ($value): float {
            if (is_numeric($value)) return (float)$value;
            if (!is_string($value)) return 0.0;
            $clean = str_replace([' VNĐ', 'đ', '%', ' '], '', $value);
            $clean = str_replace(',', '', $clean);
            $clean = str_replace('.', '', $clean);
            return (float)$clean;
        };

        // === THÊM PARSER RIÊNG CHO PHẦN TRĂM ===
        $parsePercent = function ($value): float {
            $clean = str_replace(['%', ' '], '', $value);
            $clean = str_replace(',', '.', $clean); // phẩy thành chấm thập phân
            $value = (float)$clean;
            return $value > 1 ? $value : $value * 100;  // nếu <=1 thì nhân 100 (0.35 → 35)
        };

        $totalRevenue = $shopsData->sum(fn($r) => $parse($r['doanh_thu'] ?? 0));
        $totalShareMoney = $shopsData->sum(fn($r) => $parse($r['thanh_toan'] ?? 0));
        $totalOrders = $data['total_orders_real'] ?? $shopsData->filter(fn($r) => $parse($r['doanh_thu'] ?? 0) > 0)->count();

        // === SỬA: Dùng $parsePercent để tính tỷ lệ % ===
        $sharePercentValue = $shareType === 'fixed'
            ? $shopsData->map(fn($r) => $parsePercent($r['chia_se'] ?? 0))->avg()
            : ($totalRevenue > 0 ? $shopsData->avg(fn($r) => $parsePercent($r['chia_se'] ?? 0)) : 0);

        // Log trước insert (giữ nguyên hoặc thêm nếu cần)
        Log::info('LOGSHARE - DỮ LIỆU TRƯỚC INSERT MERCHANT ' . $merchant->id, [
            'username' => $merchant->username,
            'shop_data_count' => $shopsData->count(),
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'totalShareMoney' => $totalShareMoney,
            'sharePercent' => $sharePercentValue,
            'shareType' => $shareType,
            'sample_shop' => $shopsData->first() ?? 'Rỗng',
        ]);

        if ($totalShareMoney > 0) {
            MerchantShareLog::create([
                'merchant_id' => $merchant->id,
                'year' => $data['from_year'] ?? now()->year,
                'month' => $data['from_month'] ?? now()->month,
                'contract_no' => $data['hop_dong_so'] ?? null,
                'customer_name' => $data['ben_b'] ?? null,
                'date' => now()->toDateString(),
                'number_of_order' => (int)$totalOrders,
                'share_percent' => $sharePercentValue,
                'total' => $totalRevenue,
                'share_money' => $totalShareMoney,
                'type' => $type,
                'share_type' => $shareType,
                'status' => in_array($status, ['sent', 'failed']) ? $status : ($status ? 'failed' : 'sent'),
            ]);

            Log::info("Log inserted THÀNH CÔNG merchant {$merchant->id}");
        } else {
            Log::warning("Bỏ qua insert vì totalShareMoney = 0 cho merchant {$merchant->id}");
        }
    }
}
