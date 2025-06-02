<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Làm sạch dữ liệu
                $clean = fn($v) => trim(str_replace("\t", "", $v ?? ''));

//                dd($row);
                // Tách khu vực từ shop
                $shopName = $clean($row['shop_name']); // 'Shop name'
                preg_match('/\((.*?)\)/', $shopName, $matches);
                list($region, $city, $area) = explode('-', $matches[1] ?? '--');
                Order::updateOrCreate([
                    'order_number' => $clean($row['order_number']), // 'Order Number'
                ],[
                    'order_number' => $clean($row['order_number']), // 'Order Number'
                    'payment_order_id' => $clean($row['payment_order_id']), // 'Payment Order ID'
                    'reason_for_payment_failure' => $row['reason_for_payment_failure'], // 'Reason for Payment Failure'
                    'user_id' => $row['user_id'], // 'User ID'
                    'rent_out_from' => $clean($row['rent_out_from']), // 'Rent out from'
                    'return_to' => $clean($row['return_to']), // 'Return to'
                    'when_to_rent' => $this->parseDate($clean($row['when_to_rent'] ?? null)),  // 'When to rent'
                    'when_to_return' => $this->parseDate($clean($row['when_to_return'] ?? null)), // 'When to Return'
                    'merchant_id_rent_out_from' => $clean($row['merchant_id_rent_out_from']), // 'Merchant ID rent out from'
                    'merchant_rent_out_from' => $clean($row['merchant_rent_out_from']), // 'Merchant rent out from'
                    'merchant_return_to' => $clean($row['merchant_return_to']), // 'Merchant return to'
                    'renting_time' => $clean($row['renting_time']), // 'Renting time'
                    'order_bills' => $row['order_bills'], // 'Order bills'
                    'order_bills_vnd' => floatval($row['order_billsvnd']), // 'Order bills（VND）'
                    'commission_fees' => $row['commission_fees'], // 'Commission fees'
                    'commission_fees_vnd' => floatval(str_replace('VND', '', $row['commission_feesvnd'])), // 'Refund'
                    'status_of_order' => $row['status_of_order'], // 'Status of Order'
                    'order_belongs_to' => $clean($row['order_belongs_to']), // 'Order belongs to'
                    'merchant_id' => $clean($row['merchant_id']), // 'merchant ID'
                    'name_of_merchant' => $row['name_of_merchant'], // 'Name of Merchant'
                    'staff_id' => $clean($row['staff_id']), // 'Staff ID'
                    'staff_name' => $clean($row['staff_name']), // 'Staff name'
                    'order_comes_from' => $row['order_comes_from'], // 'Order comes from'
                    'when_to_pay' => $this->parseDate($clean($row['when_to_pay'] ?? null)), // 'When to pay'
                    'payment_channel' => $row['payment_channel'], // 'Payment Channel'
                    'status_of_refund' => $row['status_of_refund'], // 'Status of Refund'
                    'refund' => floatval(str_replace('VND', '', $row['refund'])), // 'Refund'
                    'commission_of_refunds' => floatval($row['commission_of_refunds']), // 'Commission of Refunds'
                    'profit_sharing_to_dealer' => floatval($row['profit_sharing_to_dealer']), // 'Profit-sharing to dealer'
                    'revenue_to_dealer' => floatval($row['accured_revenue_to_dealer']), // 'Accured revenue to Dealer'
                    'revenue_to_merchant' => floatval($row['accured_revenue_to_merchant']), // 'Accured revenue to Merchant'
                    'billing_strategy' => $row['billing_strategy'], // 'Billing Strategy'
                    'shop_name' => $shopName,
                    'shop_type' => $row['shop_type'], // 'Shop type'
                    'location' => $row['location'], // 'Location'
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
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            } else {
                return \Carbon\Carbon::parse($value);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
