<?php

namespace App\Services;

use App\Models\Shop;
use PhpOffice\PhpWord\TemplateProcessor;

class BBNTExportService
{
    public function generateBBNTDocx(Shop $shop): string
    {
        $contract = $shop->contract;

        $template = new TemplateProcessor(storage_path('app/templates/bbnt_template.docx'));

        $template->setValue('shop_name', $shop->shop_name);
        $template->setValue('address', $shop->address);
        $template->setValue('customer_name_contract', $contract->customer_name ?? '-');
        $template->setValue('customer_position', $contract->customer_position ?? '-');

        // Ngày/tháng/năm
        $template->setValue('ngay', now()->format('d'));
        $template->setValue('thang', now()->format('m'));
        $template->setValue('nam', now()->format('Y'));

        // Tạo bảng dữ liệu thiết bị + sản phẩm
        $rows = $this->buildDeviceProductTable($shop->device_json, $shop->product_json);

        // Gỡ lỗi để kiểm tra dữ liệu
        if (empty($rows)) {
            \Log::warning('No rows generated for BBNT document for shop ID: ' . $shop->id);
        } else {
            \Log::info('Generated rows for BBNT document:', $rows);
        }

        // Clone dòng trong Word
        if (!empty($rows)) {
            $template->cloneRowAndSetValues('d', $rows); // 'd' chính là tiền tố placeholder ${d#dn}, ${d#cd}, ...
        } else {
            $template->setValue('d', 'Không có dữ liệu thiết bị hoặc sản phẩm.');
        }

        // Lưu file
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $fileName = 'bbnt_' . $shop->id . '_' . now()->format('Ymd_His') . '.docx';
        $path = $tempDir . '/' . $fileName;

        $template->saveAs($path);

        return $path;
    }

    protected function buildDeviceProductTable(?array $deviceJson, ?array $productJson): array
    {
        $rows = [];

        // Xử lý thiết bị
        $devices = $deviceJson['devices'] ?? [];
        $grouped = [];

        foreach ($devices as $device) {
            $name = strtoupper(trim($device['name'] ?? ''));
            $code = trim($device['code'] ?? '');
            $note = $device['note'] ?? '';
            $unit = 'Máy';

            if (!$name) continue;

            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'codes' => [],
                    'count' => 0,
                    'unit' => $unit,
                    'note' => $note,
                ];
            }

            if ($code) $grouped[$name]['codes'][] = $code;
            $grouped[$name]['count']++;
        }

        $sttd = 1;
        foreach ($grouped as $deviceName => $info) {
            $chunks = array_chunk(array_unique($info['codes']), 4);
            $codeList = collect($chunks)->map(fn($group) => implode(', ', $group))->implode(' ');

            $rows[] = [
                'd' => $sttd,
                'dn' => $deviceName,
                'cd' => $codeList,
                'du' => $info['unit'],
                'qty' => $info['count'],
                'dnote' => $info['note'] ?? '',
            ];

            $sttd++;
        }

        // Xử lý sản phẩm (nếu có)
        if (is_array($productJson) && isset($productJson['products'])) {
            foreach ($productJson['products'] as $product) {
                $rows[] = [
                    'd' => $sttd,
                    'dn' => $product['name'] ?? '',
                    'cd' => $product['code'] ?? '',
                    'du' => $product['unit'] ?? 'Cái',
                    'qty' => $product['quantity'] ?? '',
                    'dnote' => $product['note'] ?? '',
                ];
                $sttd++;
            }
        }

        return $rows;
    }
}
