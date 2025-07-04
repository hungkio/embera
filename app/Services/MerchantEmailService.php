<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Email;
use App\Models\EmailContent;
use App\Mail\MerchantEmail;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MerchantEmailService
{
    public function sendMail(Merchant $merchant): void
    {
        $shops = $merchant->shops()->where('shops.is_deleted', false)->get();

        if (!$shops || $shops->isEmpty()) {
            Log::warning("Merchant ID {$merchant->id} has no shop attached.");
            return;
        }

        // Chuẩn bị dữ liệu
        $data = $this->prepareData($merchant, $shops);

        // Ghi log gửi mail (render nội dung trước để lưu)
        $html = view('admin.emails.merchant_revenue', ['content' => $data])->render();

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
            Mail::to($merchant->email)->queue(new MerchantEmail($data));
            $email->update(['status' => 'sent']);
            Log::info("Email queued successfully for merchant ID {$merchant->id}");
        } catch (\Exception $e) {
            $email->update(['status' => 'failed']);
            Log::error("Failed to send email to merchant ID {$merchant->id}: {$e->getMessage()}");
        }
        Log::debug('Shop data:', $data['shop_data']);

    }

    public function prepareData(Merchant $merchant, $shops = []): array
    {
        $today = now();
        $lastMonth = Carbon::now()->subMonth();
        $bd = $merchant->admin;

        $data = [
            'hom_nay_ngay'   => $today->format('d'),
            'hom_nay_thang'  => $today->format('m'),
            'hom_nay_nam'    => $today->format('Y'),

            'hop_dong_so' => $merchant->contract->contract_number ?? '',
            'ben_b' => $merchant->contract->customer_name ?? '',
            'chuc_vu' => $bd->roles()->first()->name ?? '',
            'so_dien_thoai' => $bd->phone ?? '',
            'email' => $bd->email ?? '',

            'ten_ngan_hang' => $merchant->contract->bank_info ?? '',
            'chu_tai_khoan' => $merchant->contract->bank_account_name ?? '',
            'so_tai_khoan' => $merchant->contract->bank_account_number ?? '',
            'giam_doc_ky' => trim($bd->last_name . ' ' . $bd->first_name),

            'from_day' => $lastMonth->firstOfMonth()->format('d'),
            'from_month' => $lastMonth->firstOfMonth()->format('m'),
            'from_year' => $lastMonth->firstOfMonth()->format('Y'),
            'to_day' => $lastMonth->endOfMonth()->format('d'),
            'to_month' => $lastMonth->endOfMonth()->format('m'),
            'to_year' => $lastMonth->endOfMonth()->format('Y'),
        ];

        $shops_data = [];
        $totalPayment = 0;

        foreach ($shops as $key => $shop) {
            $fullShopName = $shop->shop_name ?? ''; // tên đầy đủ để truy query
            $displayShopName = trim(Str::before($fullShopName, '(')); // hiển thị
            $address = $shop->address ?? '';

            // Truy vấn doanh thu tháng trước
            $revenue = Order::query()
                ->whereRaw('LOWER(rental_shop) = ?', [Str::lower($shop->shop_name)])
                ->sum('order_amount');


            $shareRate = $shop->share_rate ?? 0;
            $payment = $revenue * ($shareRate / 100);
                $totalPayment += $payment;

            // Log debug chi tiết
            Log::info("Shop: {$shop->shop_name} — Revenue: {$revenue} — Share rate: {$shareRate} — Payment: {$payment}");

            $shops_data[] = [
                'stt' => $key + 1,
                'shop_name' => $displayShopName,
                'dia_chi_shop' => $address,
                'doanh_thu' => number_format($revenue, 0, ',', '.'),
                'chia_se' => number_format($shareRate, 2, '.', '') . '%',
                'thanh_toan' => number_format($payment, 0, ',', '.') . ' VNĐ',
            ];
        }

        $data['shop_data'] = $shops_data;
        $data['tong_thanh_toan'] = number_format($totalPayment, 0, ',', '.') . ' VNĐ';
        $data['tong_thanh_toan_text'] = $this->number_to_vietnamese($totalPayment);

        return $data;
    }

    private function number_to_vietnamese($number): string
    {
        $formatter = new \NumberFormatter("vi", \NumberFormatter::SPELLOUT);
        $text = $formatter->format($number);
        return ucfirst($text) . ' đồng';
    }
}
