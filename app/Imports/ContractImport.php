<?php

namespace App\Imports;

use App\Domain\Admin\Models\Admin;
use App\Models\Contract;
use App\Models\Merchant;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ContractImport implements ToCollection, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            // Gom theo contract_number
            $grouped = $rows->groupBy(fn($row) => trim($row[0]));

            if ($grouped['contract_number']) {
                unset($grouped['contract_number']);
            }
            foreach ($grouped as $contractNumber => $items) {
                $firstRow = $items->first();

                $contractNumber = trim($firstRow[0] ?? '');
                $signDate = $this->parseDate($firstRow[1] ?? null);
                $expiredDate = $this->parseDate($firstRow[2] ?? null);
                $status = trim($firstRow[3] ?? 'chưa_ký');
                $bankInfo = trim($firstRow[4] ?? '');
                $bankNumber = trim($firstRow[5] ?? '');
                $bankName = trim($firstRow[6] ?? '');
                $merchantEmail = trim($firstRow[7] ?? '');
                $merchantPhone = trim($firstRow[8] ?? '');
                $adminName = trim($firstRow[9] ?? '');

                $shopName = trim($firstRow[10] ?? '');
                $title = trim($firstRow[11] ?? '');
                $ceoSign = trim($firstRow[12] ?? '');
                $location = trim($firstRow[13] ?? '');
                $shopType = trim($firstRow[14] ?? '');
                $merchantName = trim($firstRow[15] ?? '');
                $shareRate = trim($firstRow[16] ?? '');
                $merchantUsername = trim($firstRow[20] ?? '');
                $merchantPassword = trim($firstRow[21] ?? '');


                if (!$adminName) {
                    continue;
                }
                // Tìm admin_id từ tên
                $admin = Admin::whereRaw("CONCAT(first_name, ' ', last_name) = ?", $adminName)->first();

                if (!$admin) {
                    throw new \Exception("Không tìm thấy BD: $adminName");
                }

                // Merchant
                $merchant = Merchant::updateOrCreate(
                    [
                        'username' => $merchantUsername,
                    ],
                    [
                        'email' => $merchantEmail,
                        'phone' => $merchantPhone,
                        'password' => $merchantPassword, // Có thể thay bằng logic khác
                        'admin_id' => $admin->id,
                    ]
                );

                // Gom thiết bị
                $devices = [];
                foreach ($items as $row) {
                    $deviceCode = trim($row[17] ?? '');
                    $deviceName = trim($row[18] ?? '');
                    $pin = (int) $row[19];


                    if ($deviceName) {
                        $devices[] = [
                            'code' => $deviceCode,
                            'name' => $deviceName,
//                            'quantity' => $quantity,
                            'pin' => $pin,
                        ];
                    }
                }
                $devices = collect($devices);
                $result = $devices->groupBy('device_code')->map(function ($group) {
                    $first = $group->first();
                    $first['quantity'] = $group->count();
                    return $first;
                })->values()->all();
                $deviceJson = json_encode(['devices' => $devices]);

                // Contract
                $expiredTime = null;
                if ($signDate && $expiredDate) {
                    $expiredTime = $signDate->diffInMonths($expiredDate) . ' tháng';
                }

                $contract = Contract::updateOrCreate(
                    ['contract_number' => $contractNumber],
                    [
                        'sign_date' => $signDate,
                        'expired_date' => $expiredDate,
                        'status' => $status,
                        'expired_time' => $expiredTime,
                        'bank_info' => $bankInfo,
                        'bank_account_number' => $bankNumber,
                        'bank_account_name' => $bankName,
                        'email' => $merchantEmail,
                        'phone' => $merchantPhone,
                        'admin_id' => $admin->id,
                        'merchant_id' => $merchant->id,
                        'title' => $title,
                        'ceo_sign' => $ceoSign,
                        'location' => $location,
                    ]
                );

                // Shop
                if (isset($shopName) && preg_match('/\((.*?)\)/', $shopName, $matches)) {
                    $parts = explode('-', $matches[1]);
                    $region = $parts[0] ?? null;
                    $city   = $parts[1] ?? null;
                    $area   = $parts[2] ?? null;
                }
                Shop::updateOrCreate(
                    [
                        'shop_name' => $shopName,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'address' => $location,
                        'shop_type' => $shopType,
                        'share_rate' => $shareRate*100,
                        'contact_phone' => $merchantPhone,
                        'strategy' => '(VND-1h)5-10000-52000',
                        'area' => trim($area),
                        'city' => trim($city),
                        'region' => trim($region),
                        'device_json' => ['devices' => $result],
                        'admin_id' => $admin->id,
                    ]
                );
            }
        }, 1);
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return Carbon::parse($date);
            } else {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
