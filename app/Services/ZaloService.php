<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Support\Facades\Log;

class ZaloService
{
    public function sendToMerchants(array $merchantIds, string $configKeyFixed, string $configKeyPercentage, MerchantEmailService $emailService): array
    {
        $results = [];
        // Lấy giá trị trực tiếp từ file .env
        $templateFixedId = env('OA_TEMPLATE_COUNT', 466895);
        $templatePercentageId = env('OA_TEMPLATE_PERCENT', 466893);

        // Tải merchants với điều kiện rõ ràng cho shops
        $merchants = Merchant::with([
            'contract',
            'shops' => function ($query) {
                $query->where('shops.is_deleted', false);
            }
        ])->whereIn('id', $merchantIds)->get();

        foreach ($merchants as $merchant) {
            $id = $merchant->id;
            $rawPhone = $merchant->phone;

            // Kiểm tra số điện thoại hợp lệ
            if (!$rawPhone || !preg_match('/^(0|\+84)(\d{9})$/', $rawPhone)) {
                $results[$id] = ['success' => false, 'error' => 'Invalid or missing phone number'];
                Log::warning("Merchant ID {$id} skipped: Invalid phone number {$rawPhone}");
                continue;
            }

            // Chuẩn hóa số điện thoại
            $phone = '84' . ltrim($rawPhone, '0');

            if (!$merchant->contract || $merchant->shops->isEmpty()) {
                $results[$id] = ['success' => false, 'error' => 'Missing contract or shops'];
                continue;
            }

            // Chuẩn bị dữ liệu từ MerchantEmailService
            $data = $emailService->prepareData($merchant, $merchant->shops);
            $shareType = $emailService->detectType($merchant->shops);

            // Chu kỳ giao dịch
            $lastMonth = now()->subMonth();
            $thangGiaodich = $lastMonth->format('m/Y');

            // Cấu trúc dữ liệu chung cho Zalo
            $templateData = [
                'thang_giao_dich' => $thangGiaodich,
                'ma_hop_dong' => $merchant->contract->contract_number ?? '',
                'customer_name' => $data['ben_b'] ?? '',
            ];

            // Chọn template và bổ sung params riêng
            if ($shareType === 'fixed') {
                $templateId = $templateFixedId;
                $templateData['number_of_order'] = (int) str_replace(['.', ' VNĐ'], '', $data['shop_data'][0]['doanh_thu'] ?? 0);
                $templateData['share_money'] = (int) str_replace(['.', ' VNĐ'], '', $data['shop_data'][0]['thanh_toan'] ?? 0);
                $templateData['share_percent'] = (int) str_replace(['.', ' VNĐ'], '', $data['shop_data'][0]['chia_se'] ?? 0);
            } else {
                $templateId = $templatePercentageId;
                $templateData['total'] = (int) str_replace(['.', ' VNĐ'], '', $data['tong_thanh_toan'] ?? 0);
                $templateData['share_money'] = (int) str_replace(['.', ' VNĐ'], '', $data['tong_thanh_toan_share'] ?? 0);
                $templateData['share_percent'] = (float) str_replace(['%'], '', $data['shop_data'][0]['chia_se'] ?? 0);
            }

            try {
                $response = sendZaloZNS($phone, $templateId, $templateData);
                $results[$id] = ['success' => true, 'response' => $response['message'] ?? ''];
                Log::info("Zalo ZNS sent successfully for merchant {$id}", ['phone' => $phone, 'templateId' => $templateId, 'response' => $response]);
            } catch (\Throwable $e) {
                Log::error("Zalo ZNS failed for merchant {$id}: {$e->getMessage()}");
                $results[$id] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }
}
