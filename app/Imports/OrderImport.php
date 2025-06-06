<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class OrderImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Làm sạch key + value
                $normalized = [];
                foreach ($row as $key => $value) {
                    $cleanKey = strtolower(trim(preg_replace('/\s+/', ' ', $key)));
                    $cleanValue = is_string($value) ? trim(preg_replace('/[\x00-\x1F\x7F]+/u', '', $value)) : $value;
                    $normalized[$cleanKey] = $cleanValue;
                }

                // Helper hàm
                $clean = fn($v) => is_string($v) ? trim(preg_replace('/[\x00-\x1F\x7F]+/u', '', $v)) : $v;
                $money = fn($v) => is_numeric($v) ? $v : (float) preg_replace('/[^0-9.]/', '', $v);

                $orderNumber = $clean($normalized['order_id'] ?? null);
                if (!$orderNumber) continue;

                $shop = $clean($normalized['rental_shop'] ?? '');
                preg_match('/\((.*?)\)/', $shop, $matches);
                list($region, $city, $area) = isset($matches[1]) ? explode('-', $matches[1]) + [null, null, null] : [null, null, null];

                Order::updateOrCreate(['order_number' => $orderNumber], [
                    'payment_id' => $clean($normalized['payment_id'] ?? null),
                    'payment_failure_reason' => $clean($normalized['payment_failure_reason'] ?? null),
                    'user_id' => $clean($normalized['user_id'] ?? null),
                    'rental_equipment_id' => $clean($normalized['rental_equipment_id'] ?? null),
                    'return_equipment_id' => $clean($normalized['return_equipment_id'] ?? null),
                    'rental_time' => $this->parseDate($normalized['rental_time'] ?? null),
                    'return_time' => $this->parseDate($normalized['return_time'] ?? null),
                    'rental_shop_id' => $clean($normalized['rental_shop_id'] ?? null),
                    'rental_shop' => $shop,
                    'rental_shop_type' => $clean($normalized['rental_shop_type'] ?? null),
                    'rental_shop_address' => $clean($normalized['rental_shop_address'] ?? null),
                    'return_shop' => $clean($normalized['return_shop'] ?? null),
                    'duration_of_use' => $clean($normalized['duration_of_use'] ?? null),
                    'currency' => $clean($normalized['currency'] ?? null),
                    'order_amount' => $money($normalized['order_amount'] ?? 0),
                    'fees' => $money($normalized['fees'] ?? 0),
                    'order_status' => $clean($normalized['order_status'] ?? null),
                    'orders_belong_to_merchants' => $clean($normalized['orders_belong_to_merchants'] ?? null),
                    'merchant_id' => $clean($normalized['merchant_id'] ?? null),
                    'merchant_name' => $clean($normalized['merchant_name'] ?? null),
                    'service_provider_id' => $clean($normalized['service_provider_id'] ?? null),
                    'employee_id' => $clean($normalized['employee_id'] ?? null),
                    'employee_name' => $clean($normalized['employee_name'] ?? null),
                    'order_source' => $clean($normalized['order_source'] ?? null),
                    'payment_time' => $this->parseDate($normalized['payment_time'] ?? null),
                    'payment_channels' => $clean($normalized['payment_channels'] ?? null),
                    'refund_status' => $clean($normalized['refund_status'] ?? null),
                    'refund_amount' => $money($normalized['refund_amount'] ?? 0),
                    'refund_fee' => $money($normalized['refund_fee'] ?? 0),
                    'agent_share_ratio' => (float) $clean($normalized['agent_share_ratio'] ?? 0),
                    'franchisee_share_ratio' => (float) $clean($normalized['franchisee_share_ratio'] ?? 0),
                    'service_provider_share_ratio' => (float) $clean($normalized['service_provider_share_ratio'] ?? 0),
                    'merchant_share_ratio' => (float) $clean($normalized['merchant_share_ratio'] ?? 0),
                    'charging_strategy' => $clean($normalized['charging_strategy'] ?? null),
                    'region' => trim($region),
                    'city' => trim($city),
                    'area' => trim($area),
                ]);
            }
        }, 1);
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value);
            } else {
                return \Carbon\Carbon::parse($value);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
