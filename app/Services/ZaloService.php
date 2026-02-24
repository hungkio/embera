<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ZaloService
{
    public function sendZaloContract(array $merchantIds, MerchantEmailService $emailService): array
    {
        $results = [];

        // 1. Lấy ID template từ .env (hoặc dùng số cứng 527244)
        $templateId = env('OA_TEMPLATE_CONTRACT', 527244);

        // 2. Load merchants kèm contract và shops
        // Cần load shops để hàm prepareData hoạt động được
        $merchants = Merchant::with([
            'contract',
            'shops' => function ($query) {
                $query->where('shops.is_deleted', false);
            }
        ])->whereIn('id', $merchantIds)->get();

        foreach ($merchants as $merchant) {
            $id = $merchant->id;
            $rawPhone = $merchant->phone;

            // --- Validate Phone (Giống hệt hàm cũ) ---
            if (!$rawPhone || !preg_match('/^(0|\+84)(\d{9})$/', $rawPhone)) {
                $results[$id] = ['success' => false, 'error' => 'Invalid or missing phone number'];
                Log::warning("Merchant ID {$id} skipped: Invalid phone number");
                continue;
            }
            $phone = '84' . ltrim($rawPhone, '0');

            // --- Validate Contract ---
            if (!$merchant->contract) {
                $results[$id] = ['success' => false, 'error' => 'Missing contract'];
                continue;
            }

            // --- CHUẨN BỊ DỮ LIỆU ĐỂ LẤY TÊN "BÊN B" ---
            try {
                $shops = $merchant->shops()->where('shops.is_deleted', false)->get();

                // Gọi prepareData: Dù template này không cần doanh thu, nhưng ta cần hàm này
                // để nó trích xuất tên 'ben_b' (Tên pháp nhân/Tên cửa hàng chuẩn)
                // Truyền ngày giả lập để hàm không lỗi

                // Lấy tên Bên B (ưu tiên), nếu không có mới lấy username
                // Logic này đảm bảo tin nhắn: "Kính gửi Quý Đối tác Công ty ABC" thay vì "user123"
                $tenDoiTac = $merchant->contract->customer_name ?? '';

                // Cắt tên nếu quá dài (Zalo giới hạn)
                if (mb_strlen($tenDoiTac) > 30) {
                    $tenDoiTac = mb_substr($tenDoiTac, 0, 27) . '...';
                }

                $templateData = [
                    'customer_name' => $tenDoiTac,
                    'ma_hop_dong' => $merchant->contract->contract_number ?? 'Đang cập nhật'
                ];

                // --- GỬI ZALO (Code cũ) ---
                $response = sendZaloZNS($phone, $templateId, $templateData);

                if ($response && isset($response['error']) && $response['error'] == 0) {
                    $results[$id] = ['success' => true, 'response' => $response['message'] ?? 'Sent'];
                    Log::info("Zalo Contract sent merchant {$id}", ['phone' => $phone, 'data' => $templateData]);
                } else {
                    $msg = $response['message'] ?? 'Zalo Error';
                    $results[$id] = ['success' => false, 'error' => $msg];
                    Log::error("Zalo fail merchant {$id}: {$msg}");
                }

            } catch (\Throwable $e) {
                $results[$id] = ['success' => false, 'error' => $e->getMessage()];
                Log::error("Zalo Exception merchant {$id}: {$e->getMessage()}");
            }
        }

        return $results;
    }

    public function sendToMerchants(
        array                $merchantIds,
        string               $configKeyFixed,
        string               $configKeyPercentage,
        MerchantEmailService $emailService,
        ?Carbon              $startDate = null,
        ?Carbon              $endDate = null
    ): array
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

            // Chu kỳ giao dịch từ 01/04/năm hiện tại đến hiện tại
            $startDate = $startDate ?: Carbon::createFromDate(2025, 1, 15);
            $endDate = $endDate ?: Carbon::createFromDate(2026, 1, 31);

            $thangGiaodich = 'Từ ' . $startDate->format('d/m/Y') . ' đến ' . $endDate->format('d/m/Y');

            // Chuẩn bị dữ liệu từ MerchantEmailService với khoảng thời gian
            $shops = $merchant->shops()->where('shops.is_deleted', false)->get();
            Log::info('DEBUG startDate', [$startDate, $endDate]);
            $data = $emailService->prepareData($merchant, $shops, $startDate, $endDate);
            $shareType = $emailService->detectType($shops);

            // Cấu trúc dữ liệu chung cho Zalo
            $templateData = [
                'thang_giao_dich' => $thangGiaodich,
                'ma_hop_dong' => $merchant->contract->contract_number ?? '',
                'customer_name' => $data['ben_b'] ?? '',
            ];

            // Chọn template và bổ sung params riêng
            if ($shareType === 'fixed') {
                $templateId = $templateFixedId;
                $templateData['number_of_order'] = (int)($data['tong_dong_hang'] ?? 0);
                $templateData['share_money'] = $chia_se = (int)str_replace(['.', ' VNĐ'], '', $data['tong_thanh_toan_share'] ?? 0);
                $templateData['share_percent'] = (int)str_replace(['.', ' VNĐ'], '', $data['chia_se'] ?? 0);
            } else {
                $templateId = $templatePercentageId;
                $templateData['total'] = (int)str_replace(['.', ' VNĐ'], '', $data['tong_thanh_toan'] ?? 0);
                $templateData['share_money'] = $chia_se = (int)str_replace(['.', ' VNĐ'], '', $data['tong_thanh_toan_share'] ?? 0);
                $templateData['share_percent'] = (float)str_replace(['%'], '', $data['shop_data'][0]['chia_se'] ?? 0);
            }

            try {
                if ($chia_se) {
                    $response = sendZaloZNS($phone, $templateId, $templateData);
                    $results[$id] = ['success' => true, 'response' => $response['message'] ?? ''];
                    Log::info("Zalo ZNS sent successfully for merchant {$id}", ['phone' => $phone, 'templateId' => $templateId, 'response' => $response]);
                } else {
                    $results[$id] = ['success' => true, 'response' => 'Không gửi doanh thu do không có số tiền chia sẻ'];
                }
            } catch (\Throwable $e) {
                Log::error("Zalo ZNS failed for merchant {$id}: {$e->getMessage()}");
                $results[$id] = ['success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }
}
