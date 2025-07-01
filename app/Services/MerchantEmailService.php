<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Email;
use App\Models\EmailContent;
use App\Mail\MerchantEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;

class MerchantEmailService
{
    public function sendMail(Merchant $merchant): void
    {

        $shops = $merchant->shops;

        if (!$shops) {
            Log::warning("Merchant ID {$merchant->id} has no shop attached.");
            return;
        }

        $data = $this->prepareData($merchant, $shops);
        $html = $this->generateHtmlFromDocx(
            storage_path('app/templates/hd_xac_nhan_doanh_thu.docx'),
            $data
        );


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
            Mail::to($merchant->email)->queue(new MerchantEmail($html));
            $email->update(['status' => 'sent']);
            Log::info("Email queued successfully for merchant ID {$merchant->id}");
        } catch (\Exception $e) {
            $email->update(['status' => 'failed']);
            Log::error("Failed to send email to merchant ID {$merchant->id}: {$e->getMessage()}");
        }
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
            'giam_doc_ky' => $merchant->contract->ceo_sign ?? '',

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
            $shopName = trim(Str::before($shop->shop_name ?? '', '('));
            $address = $shop->address ?? '';

            $revenue = $shop->contract->total_revenue ?? 0;
            $shareRate = $shop->contract->share_ratio ?? 0;
            $payment = $revenue * ($shareRate / 100);

            $totalPayment += $payment;

            $shops_data[] = [
                'stt' => $key + 1,
                'shop_name' => $shopName,
                'dia_chi_shop' => $address,
                'doanh_thu' => number_format($revenue, 0, ',', '.'),
                'chia_se' => $shareRate,
                'thanh_toan' => number_format($payment, 0, ',', '.'),
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

    public function generateHtmlFromDocx(string $templatePath, array $data): string
    {
        $processor = new TemplateProcessor($templatePath);

        if (!empty($data['shop_data'])) {
            $processor->cloneRow('stt', count($data['shop_data']));
            foreach ($data['shop_data'] as $index => $shop) {
                foreach ($shop as $key => $value) {
                    $processor->setValue("{$key}#" . ($index + 1), $value);
                }
            }
        }

        unset($data['shop_data']);

        foreach ($data as $key => $value) {
            $processor->setValue($key, htmlspecialchars((string)$value));
        }


        $tempPath = storage_path('app/generated_' . uniqid() . '.docx');
        $processor->saveAs($tempPath);

        $phpWord = IOFactory::load($tempPath);
        $writer = IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
