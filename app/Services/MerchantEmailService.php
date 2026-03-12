<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Email;
use App\Models\EmailContent;
use App\Mail\MerchantEmail;
use App\Models\MerchantShareLog;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MerchantEmailService
{
    public function sendMail(Merchant $merchant, array $data, ?Carbon $startDate = null, ?Carbon $endDate = null): void
    {
        $status = 'sent'; // mặc định là thành công
        $failReason = null;

        // Gán $shops ngay từ đầu, để dùng cho mọi logic sau này
        $shops = $merchant->shops()->where('shops.is_deleted', false)->get();

        if (empty(trim($merchant->email))) {
            $status = 'failed';
            $failReason = 'no_email';
            Log::warning("Merchant ID {$merchant->id} skipped: no email address.");
        } elseif ($shops->isEmpty()) {
            $status = 'failed';
            $failReason = 'no_shop';
            Log::warning("Merchant ID {$merchant->id} has no shop attached.");
        }

        if ($status === 'failed') {
            // Dữ liệu log có thể lấy từ $data truyền vào hoặc mảng rỗng
            app(\App\Services\MerchantShareLogService::class)->logShare(
                $merchant,
                $data ?? [],
                'unknown',
                'email',
                $failReason ?? 'failed'
            );
            return;
        }

        // Chu kỳ giao dịch từ 01/04/năm hiện tại đến hiện tại 1/4 - 30/1
        $startDate = $startDate ?: Carbon::createFromDate(2025, 1, 15);
        $endDate = $endDate ?: Carbon::createFromDate(2026, 1, 31);

        // rồi gọi prepareData
        $data = $this->prepareData($merchant, $shops, $startDate, $endDate);

        // Xác định template
        $type = $this->detectType($shops);
        $view = $type === 'fixed'
            ? 'admin.emails.merchant_revenue_fixed'
            : 'admin.emails.merchant_revenue';
        $html = view($view, ['content' => $data])->render();

        // Lưu Email / EmailContent trong db
        $email = Email::create([
            'to' => $merchant->email,
            'merchant_id' => $merchant->id,
            'status' => 'pending',
        ]);
        EmailContent::create([
            'email_id' => $email->id,
            'text' => $html,
        ]);

        try {
            Mail::to($merchant->email)
                ->queue(new MerchantEmail($data, $type));
            $email->update(['status' => 'sent']);
            $status = 'sent';
            Log::info("Email queued successfully for merchant ID {$merchant->id}");
        } catch (\Exception $e) {
            $email->update(['status' => 'failed']);
            $status = 'failed';
            Log::error("Failed to send email to merchant ID {$merchant->id}: {$e->getMessage()}");
        }
        app(\App\Services\MerchantShareLogService::class)->logShare($merchant, $data, $type, 'email', $status);
    }

    public function detectType($shops): string
    {
        $unique = $shops->pluck('share_rate_type')->unique();
        return ($unique->count() === 1 && $unique->first() === 'fixed')
            ? 'fixed'
            : 'percentage';
    }

    private function createShareLog(Merchant $merchant, array $data, string $type): void
    {
        // 1) Lấy lại danh sách shop (dành cho tính order count)
        $shops = $merchant->shops()
            ->where('shops.is_deleted', false)
            ->get();

        // 2) Reconstruct period from data
        $fromDate = Carbon::create($data['from_year'], $data['from_month'], $data['from_day']);
        $toDate = Carbon::create($data['to_year'], $data['to_month'], $data['to_day']);
        $periodStartLog = $fromDate->copy()->startOfDay();
        $periodEndLog = $toDate->copy()->endOfDay();

        $totalOrders = $shops->sum(function ($shop) use ($periodStartLog, $periodEndLog) {
            return Order::whereRaw('LOWER(rental_shop) LIKE LOWER(?)', ['%' . $shop->shop_name . '%'])
                ->where('order_amount', '>', 0)
                ->whereBetween('return_time', [$periodStartLog, $periodEndLog])
                ->count();
        });

        // 3) Lấy shop_data đã format xong ở prepareData()
        $shopsData = collect($data['shop_data'] ?? []);

        // 4) Parser chung: xóa dấu . VNĐ %
        $parse = function (string $s): float {
            return (float)str_replace(['.', ' VNĐ', '%'], '', $s);
        };

        // 5) Tính tổng doanh thu và tổng tiền chia
        $totalRevenue = $shopsData->sum(fn($r) => $parse($r['doanh_thu']));
        $totalShareMoney = $shopsData->sum(fn($r) => $parse($r['thanh_toan']));

        // 6) Xác định share_percentValue qua if/else
        if ($type === 'fixed') {
            // Với fixed:
            //    - lấy giá trị chia cố định từ mỗi shop: $parse($r['chia_se'])
            //    - trung bình nếu có nhiều shop, hoặc chỉ first() nếu chỉ có 1 shop
            $fixedRates = $shopsData->map(fn($r) => $parse($r['chia_se']));
            $sharePercentValue = $fixedRates->count()
                ? $fixedRates->avg()
                : 0;
        } else {
            $sharePercentValue = $totalRevenue > 0
                ? round(($totalShareMoney / $totalRevenue) * 100, 0)
                : 0;
        }

        // 7) Tạo bản ghi log
        MerchantShareLog::create([
            'merchant_id' => $merchant->id,
            'year' => $data['from_year'] ?? now()->year,
            'month' => $data['from_month'] ?? now()->month,
            'contract_no' => $data['hop_dong_so'] ?? null,
            'customer_name' => $data['ben_b'] ?? null,
            'date' => now()->toDateString(),
            'number_of_order' => $totalOrders,
            'share_percent' => $sharePercentValue,
            'total' => $totalRevenue,
            'share_money' => $totalShareMoney,
            'type' => 'email',
            'share_type' => $type,
        ]);
    }

    public function prepareData(Merchant $merchant, $shops = [], Carbon $startDate = null, Carbon $endDate = null): array
    {
        $today = now();

        $periodStart = $startDate ?: Carbon::createFromDate(2025, 1, 15)->startOfDay();
        $periodEnd = $endDate ?: Carbon::createFromDate(2026, 1, 31)->endOfDay();

        $bd = $merchant->admin;
        $firstRoleName = $bd ? optional($bd->roles()->first())->name : '';
        $ten_bd = $bd ? trim(($bd->first_name ?? '') . ' ' . ($bd->last_name ?? '')) : '';

        $data = [
            'hom_nay_ngay' => $today->format('d'),
            'hom_nay_thang' => $today->format('m'),
            'hom_nay_nam' => $today->format('Y'),

            'hop_dong_so' => $merchant->contract->contract_number ?? '',
            'ben_b' => $merchant->contract->customer_name ?? $merchant->username ?? '',
            'chuc_vu' => $firstRoleName,
            'so_dien_thoai' => $bd->phone ?? '',
            'email' => $bd->email ?? '',

            'ten_ngan_hang' => $merchant->contract->bank_info ?? '',
            'chu_tai_khoan' => $merchant->contract->bank_account_name ?? '',
            'so_tai_khoan' => $merchant->contract->bank_account_number ?? '',
            'ten_bd' => $ten_bd,

            'from_day' => $periodStart->format('d'),
            'from_month' => $periodStart->format('m'),
            'from_year' => $periodStart->format('Y'),
            'to_day' => $periodEnd->format('d'),
            'to_month' => $periodEnd->format('m'),
            'to_year' => $periodEnd->format('Y'),
        ];

        $shops_data = [];
        $totalRevenue = 0;
        $totalOrder = 0;
        $totalPayment = 0;

        foreach ($shops as $key => $shop) {
            $periodStartDay = $periodStart->copy()->startOfDay();
            $periodEndDay = $periodEnd->copy()->endOfDay();

            $displayShopName = trim(Str::before($shop->shop_name ?? '', '('));
            $address = $shop->address ?? '';

            // Pattern LIKE: loại bỏ dấu ngoặc, khoảng trắng thừa
            $shopNameClean = trim(preg_replace('/\s*\([^)]*\)/', '', $shop->shop_name ?? ''));
            $shopNameClean = strtolower($shopNameClean);

            Log::debug("Query order cho shop {$shop->shop_name}", [
                'shop_name_clean' => $shopNameClean,
                'pattern' => '%' . $shopNameClean . '%',
            ]);

            $query = Order::whereRaw('TRIM(LOWER(rental_shop)) LIKE ?', ['%' . $shopNameClean . '%'])
                ->whereBetween('return_time', [$periodStartDay, $periodEndDay])
                ->where('order_status', 'Complete');

            // Tính chung cho cả fixed và percentage
            $revenue = $query->clone()->sum('order_amount');  // DOANH THU TIỀN THẬT
            $sumNumberOrder = $query->clone()->where('order_amount', '>', 0)->count();  // SỐ ĐƠN THẬT

            if ($shop->share_rate_type == 'fixed') {
                $shareRate = $shop->share_rate ?? 3000;  // fixed 3.000 VNĐ/đơn
                $payment = $sumNumberOrder * $shareRate;

                $totalOrder += $sumNumberOrder;
                $totalRevenue += $revenue;  // cộng tiền thật
                $totalPayment += $payment;

                $shops_data[] = [
                    'stt' => $key + 1,
                    'shop_name' => $displayShopName,
                    'dia_chi_shop' => $address,
                    'doanh_thu' => number_format($revenue, 0, ',', '.') . ' VNĐ',  // hiển thị tiền thật
                    'chia_se' => number_format($shareRate, 0, ',', '.') . ' VNĐ',
                    'thanh_toan' => number_format($payment, 0, ',', '.') . ' VNĐ',
                ];
            } else {
                $shareRate = $shop->share_rate ?? 0.35;  // 35% mặc định nếu không có
                $payment = $revenue * $shareRate;

                $totalRevenue += $revenue;
                $totalPayment += $payment;

                $shops_data[] = [
                    'stt' => $key + 1,
                    'shop_name' => $displayShopName,
                    'dia_chi_shop' => $address,
                    'doanh_thu' => number_format($revenue, 0, ',', '.') . ' VNĐ',
                    'chia_se' => number_format($shareRate * 100, 0) . '%',
                    'thanh_toan' => number_format($payment, 0, ',', '.') . ' VNĐ',
                    'share_rate' => $shareRate,  // THÊM DÒNG NÀY
                ];
            }
        }

        $data['shop_data'] = $shops_data;
        $data['tong_thanh_toan'] = number_format($totalRevenue, 0, ',', '.') . ' VNĐ';
        $data['tong_dong_hang'] = $totalOrder;
        $data['tong_thanh_toan_share'] = number_format($totalPayment, 0, ',', '.') . ' VNĐ';
        $data['tong_thanh_toan_text'] = $this->number_to_vietnamese($totalPayment);
        $data['total_orders_real'] = $totalOrder;
        // Log chung chung để kiểm tra
        Log::info('PREPARE DATA - KẾT QUẢ CUỐI CÙNG MERCHANT ' . $merchant->id, [
            'merchant_username' => $merchant->username,
            'period' => $periodStart->toDateString() . ' -> ' . $periodEnd->toDateString(),
            'shops_count' => $shops->count(),
            'shop_data_count' => count($shops_data),
            'tong_dong_hang' => $totalOrder,
            'tong_thanh_toan' => $totalRevenue,
            'tong_thanh_toan_share' => $totalPayment,
            'sample_shop' => $shops_data[0] ?? 'Rỗng',

        ]);

        return $data;
    }

    private function number_to_vietnamese(int $number): string
    {
        // 1. Khởi tạo NumberFormatter với locale vi_VN
        $formatter = new \NumberFormatter('vi_VN', \NumberFormatter::SPELLOUT);

        // 2. Chuyển số thành chữ
        $text = $formatter->format($number);

        // 3. Chuẩn hóa khoảng trắng và dấu gạch ngang
        $text = preg_replace('/[\s\-]+/', ' ', $text);

        // 4. Viết hoa chữ cái đầu
        $text = ucfirst(trim($text));

        // 5. Thêm từ "đồng chẵn" phía sau
        return $text . ' đồng chẵn';
    }
}
