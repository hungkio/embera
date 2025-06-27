<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Email;
use App\Models\EmailContent;
use App\Mail\MerchantEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class ContractEmailService
{
    public function sendContract(Contract $contract)
    {
        $data = $this->prepareData($contract);
        $html = $this->generateHtmlFromDocx(storage_path('app/templates/hd_xac_nhan_doanh_thu.docx'), $data);

        $merchant = $contract->shop->merchant;

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
            Log::info('Đã thêm email vào hàng đợi cho: ' . $merchant->email . ' (ID: ' . $email->id . ')');
            $email->update(['status' => 'queued']);
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm email vào hàng đợi: ' . $e->getMessage(), ['email_id' => $email->id]);
            $email->update(['status' => 'failed']);
        }
    }

    public function prepareData(Contract $contract): array
    {
        $shop = $contract->shop;
        $merchant = $shop->merchant;
        $deviceJson = $shop->device_json['devices'] ?? [];
        $today = now();

        // 👉 Tách tên shop và mã
        $fullShopName = $shop->shop_name ?? '';
        $shopName = trim(Str::before($fullShopName, '('));
        $shopCode = Str::between($fullShopName, '(', ')');

        $base = [
            'hom_nay_ngay'   => $today->format('d'),
            'hom_nay_thang'  => $today->format('m'),
            'hom_nay_nam'    => $today->format('Y'),

            'ben_b' => $shopName,
            'ma_diem' => $shopCode,
            'hop_dong_so' => $contract->contract_number ?? '',
            'ky_tu_ngay' => optional($contract->sign_date)->format('d/m/Y'),
            'ky_den_ngay' => optional($contract->expired_date)->format('d/m/Y'),
            'tong_tien' => number_format($contract->total_revenue ?? 0) . ' VNĐ',
            'giam_doc_ky' => $contract->ceo_sign ?? '',
            'ten_ngan_hang' => $contract->bank_info ?? '',
            'chu_tai_khoan' => $contract->bank_account_name ?? '',
            'so_tai_khoan' => $contract->bank_account_number ?? '',
            'dia_chi_shop' => $shop->address ?? '',
            'nguoi_lap' => $merchant->username ?? '',
            'chuc_vu' => $merchant->position ?? '',
            'so_dien_thoai' => $contract->phone ?? '',
            'email' => $merchant->email ?? '',
            'doanh_thu' => number_format($shop->share_rate ?? 0, 0, ',', '.') . ' VNĐ',
            'doanh_thu_text' => $this->number_to_vietnamese($shop->share_rate ?? 0),
        ];

        $from = optional($contract->sign_date);
        $to = optional($contract->expired_date);

        $base['from_day'] = $from->format('d');
        $base['from_month'] = $from->format('m');
        $base['from_year'] = $from->format('Y');

        $base['to_day'] = $to->format('d');
        $base['to_month'] = $to->format('m');
        $base['to_year'] = $to->format('Y');

        $deviceData = $this->extractDeviceData($deviceJson);

        return array_merge($base, $deviceData);
    }

    function number_to_vietnamese($number)
    {
        $formatter = new \NumberFormatter("vi", \NumberFormatter::SPELLOUT);
        $text = $formatter->format($number);
        return ucfirst($text) . ' đồng';
    }

    private function extractDeviceData(array $devices): array
    {
        $map = [
            'CB8' => ['qty' => 'cb8_qty', 'share' => 'cb8_share'],
            'CB8 Pro' => ['qty' => 'cb8pro_qty', 'share' => 'cb8pro_share'],
            'CB32' => ['qty' => 'cb32_qty', 'share' => 'cb32_share'],
        ];

        $result = [
            'cb8_qty' => 0, 'cb8_share' => 0,
            'cb8pro_qty' => 0, 'cb8pro_share' => 0,
            'cb32_qty' => 0, 'cb32_share' => 0,
        ];

        foreach ($devices as $device) {
            $name = trim($device['name'] ?? '');
            if (isset($map[$name])) {
                $result[$map[$name]['qty']] = $device['quantity'] ?? 0;
                $result[$map[$name]['share']] = $device['share'] ?? 0;
            }
        }

        return $result;
    }

    public function generateHtmlFromDocx(string $templatePath, array $data): string
    {
        $processor = new TemplateProcessor($templatePath);
        foreach ($data as $key => $value) {
            $processor->setValue($key, htmlspecialchars((string) $value));
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
