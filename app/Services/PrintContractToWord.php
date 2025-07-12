<?php

namespace App\Services;

use App\Models\Contract;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Log;

class PrintContractToWord
{
    public function printContractToWord(Contract $contract)
    {
        Log::info('Starting printContractToWord for contract ID: ' . $contract->id);
        $merchant = $contract->merchant;

        $data = [
            'ben_b' => $contract->customer_name ?? '',
            'dia_chi' => $contract->location ?? '',
            'giay_dkdn' => $contract->license_number ?? '',
            'nguoi_dai_dien' => $contract->customer_name ?? '',
            'chuc_vu' => $contract->customer_position ?? '',
            'cccd' => $contract->customer_cccd ?? '',
            'so_dien_thoai' => $contract->phone ?? '',
            'email' => $merchant->email ?? '',
            'chu_tai_khoan' => $contract->bank_account_name ?? '',
            'so_tai_khoan' => $contract->bank_account_number ?? '',
            'ten_ngan_hang' => $contract->bank_info ?? '',
            'contract_number' => $contract->contract_number ?? '',
            'expired_date' => optional($contract->expired_date)->format('d/m/Y') ?? '',
            'expired_time' => preg_match('/\d+/', $contract->expired_time, $m)
                ? str_pad($m[0], 2, '0', STR_PAD_LEFT)
                : '',
            'ngay' => now()->format('d'),
            'thang' => now()->format('m'),
            'nam' => now()->format('Y'),
        ];

        $templatePath = storage_path('app/templates/HDDNT.docx');
        Log::info('Using template path: ' . $templatePath);
        if (!file_exists($templatePath)) {
            Log::error('Template file not found at: ' . $templatePath);
            throw new \Exception('Template file missing at: ' . $templatePath);
        }

        try {
            $processor = new TemplateProcessor($templatePath);
            Log::info('TemplateProcessor initialized for contract ID: ' . $contract->id);
            foreach ($data as $key => $value) {
                Log::debug('Setting value for ' . $key . ': ' . $value);
                $processor->setValue($key, $value);
            }

            // Thêm bảng danh sách shop
            $shops = $contract->shops->where('is_deleted', false)->values();
            if ($shops->count()) {
                $processor->cloneRow('shop_name', $shops->count());

                foreach ($shops as $index => $shop) {
                    $row = $index + 1;

                    $processor->setValue("stt#{$row}", $row);
                    $displayName = trim(preg_replace('/\s*\(.*?\)$/', '', $shop->shop_name ?? ''));
                    $processor->setValue("shop_name#{$row}", $displayName);
                    $processor->setValue("address#{$row}", $shop->address ?? '');
                    $processor->setValue("share_rate#{$row}", $shop->share_rate ?? 0); // Add share_rate

                    $deviceMap = [
                        'CB8' => 0,
                        'CB8 PRO' => 0,
                        'CB32' => 0,
                    ];

                    // Các alias cho phép: tên từ json => chuẩn hóa về key của deviceMap
                    $aliasMap = [
                        'CB8PRO' => 'CB8 PRO',
                        'CB8 PRO' => 'CB8 PRO',
                        'CB8' => 'CB8',
                        'CB32' => 'CB32',
                        'CP8' => 'CB8', // nếu CP8 là tên khác của CB8
                    ];

                    foreach ($shop->device_json['devices'] ?? [] as $device) {
                        $rawName = strtoupper(trim($device['name'] ?? ''));
                        $mappedName = $aliasMap[$rawName] ?? null;

                        if ($mappedName && isset($deviceMap[$mappedName])) {
                            $deviceMap[$mappedName] += 1; // ✅ Mỗi thiết bị = 1 đơn vị
                            Log::debug("Counted device {$mappedName} for shop ID {$shop->id}");
                        } else {
                            Log::warning("Unrecognized device name '{$rawName}' for shop ID {$shop->id}");
                        }
                    }


                    // Fill vào từng cột thiết bị
                    $processor->setValue("dqCB8#{$row}", $deviceMap['CB8']);
                    $processor->setValue("dqCB8_PRO#{$row}", $deviceMap['CB8 PRO']);
                    $processor->setValue("dqCB32#{$row}", $deviceMap['CB32']);
                }
            } else {
                // Nếu không có shop nào
                $processor->setValue('stt', '');
                $processor->setValue('shop_name', 'Không có cửa hàng');
                $processor->setValue('address', '');
                $processor->setValue('share_rate', 0); // Add share_rate for no-shop case
                $processor->setValue('dqCB8', 0);
                $processor->setValue('dqCB8_PRO', 0);
                $processor->setValue('dqCB32', 0);
            }

            $generatedDir = storage_path('app/generated');
            if (!file_exists($generatedDir)) {
                Log::info('Creating generated directory: ' . $generatedDir);
                if (!mkdir($generatedDir, 0775, true) && !file_exists($generatedDir)) {
                    Log::error('Failed to create generated directory: ' . $generatedDir);
                    throw new \Exception('Could not create generated directory: ' . $generatedDir);
                }
            }

            $fileName = 'Hop_Dong_' . $contract->id . '_' . now()->format('Ymd_His') . '.docx';
            $outputPath = $generatedDir . '/' . $fileName;
            Log::info('Saving Word file to: ' . $outputPath);
            $processor->saveAs($outputPath);

            if (!file_exists($outputPath)) {
                Log::error('Failed to save Word file at: ' . $outputPath);
                throw new \Exception('Failed to generate Word file at: ' . $outputPath);
            }

            // ✅ Tăng số lượng download lên 1
            $contract->increment('download_count');

            Log::info('Word file generated successfully at: ' . $outputPath);
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Exception in printContractToWord: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }
    }
}
