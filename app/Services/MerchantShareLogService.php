<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\MerchantShareLog;

class MerchantShareLogService
{
    /**
     * Ghi log chia sẻ doanh thu (dùng chung cho email, zalo...)
     */
    public function logShare(Merchant $merchant, array $data, string $shareType, string $type ,string  $status = ''): void
    {
        $shopsData = collect($data['shop_data'] ?? []);

        // Parser để loại bỏ định dạng
        $parse = function (string $s): float {
            return (float) str_replace(['.', ' VNĐ', '%'], '', $s);
        };

        // Tính tổng doanh thu và tổng tiền chia
        $totalRevenue = $shopsData->sum(fn($r) => $parse($r['doanh_thu']));
        $totalShareMoney = $shopsData->sum(fn($r) => $parse($r['thanh_toan']));

        // Tính số đơn hàng
        $totalOrders = $shopsData->sum(fn($r) => $parse($r['doanh_thu']));

        // Xác định share_percent
        $sharePercentValue = $shareType === 'fixed'
            ? $shopsData->map(fn($r) => $parse($r['chia_se']))->avg()
            : ($totalRevenue > 0 ? round(($totalShareMoney / $totalRevenue) * 100, 0) : 0);

        // Ghi log
        MerchantShareLog::create([
            'merchant_id'    => $merchant->id,
            'year'           => $data['from_year'] ?? now()->year,
            'month'          => $data['from_month'] ?? now()->month,
            'contract_no'    => $data['hop_dong_so'] ?? null,
            'customer_name'  => $data['ben_b'] ?? null,
            'date'           => now()->toDateString(),
            'number_of_order'=> $totalOrders,
            'share_percent'  => $sharePercentValue,
            'total'          => $totalRevenue,
            'share_money'    => $totalShareMoney,
            'type'           => $type, // 'zalo' hoặc 'email'
            'share_type'     => $shareType,
            'status'         => in_array($status, ['sent', 'failed']) ? $status : ($status ? 'failed' : 'sent'),

        ]);
    }
}
