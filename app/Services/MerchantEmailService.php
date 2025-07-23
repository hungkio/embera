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
    public function sendMail(Merchant $merchant, array $data): void
    {
        if (empty(trim($merchant->email))) {
            Log::warning("Merchant ID {$merchant->id} skipped: no email address.");
            return;
        }

        $shops = $merchant->shops()->where('shops.is_deleted', false)->get();
        if ($shops->isEmpty()) {
            Log::warning("Merchant ID {$merchant->id} has no shop attached.");
            return;
        }

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
            Log::info("Email queued successfully for merchant ID {$merchant->id}");
        } catch (\Exception $e) {
            $email->update(['status' => 'failed']);
            Log::error("Failed to send email to merchant ID {$merchant->id}: {$e->getMessage()}");
        }

        // **Sau khi đã queue xong, ghi log chia sẻ**
        $this->createShareLog($merchant, $data, $type);
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

        $totalOrders = $shops->sum(function ($shop) {
            return Order::whereRaw('LOWER(rental_shop) = ?', [Str::lower($shop->shop_name)])
                ->where('order_amount', '>', 0)
                ->count();
        });

        // 2) Lấy shop_data đã format xong ở prepareData()
        $shopsData = collect($data['shop_data'] ?? []);

        // 3) Parser chung: xóa dấu . VNĐ %
        $parse = function (string $s): float {
            return (float)str_replace(['.', ' VNĐ', '%'], '', $s);
        };

        // 4) Tính tổng doanh thu và tổng tiền chia
        $totalRevenue = $shopsData->sum(fn($r) => $parse($r['doanh_thu']));
        $totalShareMoney = $shopsData->sum(fn($r) => $parse($r['thanh_toan']));

        // 5) Xác định share_percentValue qua if/else
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

        // 6) Tạo bản ghi log
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

    public function prepareData(Merchant $merchant, $shops = []): array
    {
        $today = now();
        $lastMonth = Carbon::now()->subMonth();

        $bd = $merchant->admin;
        $firstRoleName = $bd ? optional($bd->roles()->first())->name : '';
        $giamDocKy = $bd ? trim(($bd->last_name ?? '') . ' ' . ($bd->first_name ?? '')) : '';

        // Thông tin chung
        $data = [
            'hom_nay_ngay' => $today->format('d'),
            'hom_nay_thang' => $today->format('m'),
            'hom_nay_nam' => $today->format('Y'),

            'hop_dong_so' => $merchant->contract->contract_number ?? '',
            'ben_b' => $merchant->contract->customer_name ?? '',
            'chuc_vu' => $firstRoleName,
            'so_dien_thoai' => $bd->phone ?? '',
            'email' => $bd->email ?? '',

            'ten_ngan_hang' => $merchant->contract->bank_info ?? '',
            'chu_tai_khoan' => $merchant->contract->bank_account_name ?? '',
            'so_tai_khoan' => $merchant->contract->bank_account_number ?? '',
            'giam_doc_ky' => $giamDocKy,

            'from_day' => $lastMonth->firstOfMonth()->format('d'),
            'from_month' => $lastMonth->firstOfMonth()->format('m'),
            'from_year' => $lastMonth->firstOfMonth()->format('Y'),
            'to_day' => $lastMonth->endOfMonth()->format('d'),
            'to_month' => $lastMonth->endOfMonth()->format('m'),
            'to_year' => $lastMonth->endOfMonth()->format('Y'),
        ];

        $shops_data = [];
        $totalRevenue = 0;    // Tổng doanh thu
        $totalPayment = 0;    // Tổng tiền chia

        foreach ($shops as $key => $shop) {
            if ($shop->share_rate_type == 'fixed') {
                $displayShopName = trim(Str::before($shop->shop_name ?? '', '('));
                $address = $shop->address ?? '';

                // Tính doanh thu tháng trước
                $sumNumberOrder = Order::whereRaw('LOWER(rental_shop) = ?', [Str::lower($shop->shop_name)])->where('order_amount', '>', 0)
                    ->count();
                $shareRate = $shop->share_rate ?? 0;

                $totalRevenue += $sumNumberOrder;
                $totalPayment += $sumNumberOrder * $shareRate;

                $shops_data[] = [
                    'stt' => $key + 1,
                    'shop_name' => $displayShopName,
                    'dia_chi_shop' => $address,
                    'doanh_thu' => number_format($sumNumberOrder, 0, ',', '.'),
                    'chia_se' => number_format($shareRate, 0, ',', '.') . ' VNĐ',
                    'thanh_toan' => number_format($sumNumberOrder * $shareRate, 0, ',', '.') . ' VNĐ',
                ];
            } else {
                $displayShopName = trim(Str::before($shop->shop_name ?? '', '('));
                $address = $shop->address ?? '';

                // Tính doanh thu tháng trước
                $revenue = Order::whereRaw('LOWER(rental_shop) = ?', [Str::lower($shop->shop_name)])
                    ->sum('order_amount');
                $shareRate = $shop->share_rate ?? 0;
                $payment = $revenue * ($shareRate / 100);

                $totalRevenue += $revenue;
                $totalPayment += $payment;

                Log::info("Shop: {$shop->shop_name} — Revenue: {$revenue} — Share rate: {$shareRate} — Payment: {$payment}");

                $shops_data[] = [
                    'stt' => $key + 1,
                    'shop_name' => $displayShopName,
                    'dia_chi_shop' => $address,
                    'doanh_thu' => number_format($revenue, 0, ',', '.'),
                    'chia_se' => number_format($shareRate, 0, '.', '') . '%',
                    'thanh_toan' => number_format($payment, 0, ',', '.') . ' VNĐ',
                ];
            }
        }

        // Gán lại vào mảng data
        $data['shop_data'] = $shops_data;
        $data['tong_thanh_toan'] = number_format($totalRevenue, 0, ',', '.') . ' VNĐ';      // Tổng doanh thu
        $data['tong_thanh_toan_share'] = number_format($totalPayment, 0, ',', '.') . ' VNĐ';     // Tổng tiền chia
        $data['tong_thanh_toan_text'] = $this->number_to_vietnamese($totalPayment);

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
