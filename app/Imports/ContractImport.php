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
use Illuminate\Support\Facades\Log;

class ContractImport implements ToCollection, WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $statuses = [
            'đã ký' => 2,
            'chưa ký' => 1,
            'không ký' => 1,
        ];

        DB::transaction(function () use ($rows, $statuses) {
            // Group by shop_name (index 0), since 1 shop has 1 BD and 1 contract, but multiple devices
            $grouped = $rows->groupBy(fn($row) => trim($row[0] ?? ''));

            // Unset header group
            if ($grouped->has('shop name')) {
                $grouped = $grouped->forget('shop name');
            }

            foreach ($grouped as $groupKey => $items) {
                if ($items->isEmpty()) {
                    continue;
                }

                $firstRow = $items->first();

                $shopName = trim($firstRow[0] ?? '');
                $contractNumber = trim($firstRow[1] ?? '');
                $signDate = $this->parseDate($firstRow[2] ?? null);
                $expiredDate = $this->parseDate($firstRow[3] ?? null);
                $statusRaw = strtolower(trim($firstRow[4] ?? ''));
                $status = isset($statuses[$statusRaw]) ? $statuses[$statusRaw] : 1;
                $bankInfo = trim($firstRow[5] ?? '');
                $bankNumber = trim($firstRow[6] ?? '');
                $bankName = trim($firstRow[7] ?? '');
                $bdName = trim($firstRow[8] ?? '');
                $customerName = trim($firstRow[9] ?? '');
                $customerPosition = trim($firstRow[10] ?? '');
                $merchantEmail = trim($firstRow[11] ?? '');
                $merchantPhone = trim($firstRow[12] ?? '');
                $businessRegistration = trim($firstRow[13] ?? '');
                $customerCccd = trim($firstRow[14] ?? '');
                $location = trim($firstRow[15] ?? '');
                $title = trim($firstRow[17] ?? '');
                $ceoSign = trim($firstRow[18] ?? '');
                $shopType = trim($firstRow[19] ?? '');
                $merchantName = trim($firstRow[20] ?? '');
                $shareRate = trim($firstRow[21] ?? '');
                $merchantUsername = trim($firstRow[25] ?? '');
                $merchantPassword = trim($firstRow[26] ?? '');


                if (!$shopName || !$contractNumber) {
                    continue;
                }

                $adminName = $bdName ?: $customerPosition;
                if (!$adminName) {
                    Log::warning("Skipping row for shop '{$shopName}' due to empty BD name.");
                    continue;
                }

                // Split adminName into first_name (family name) and last_name (given name)
                $nameParts = explode(' ', $adminName);
                $firstName = array_shift($nameParts); // First word as first_name (e.g., 'Trần')
                $lastName = implode(' ', $nameParts); // Rest as last_name (e.g., 'Hải Yến')

                // Tìm or create admin
                $admin = Admin::firstOrCreate(
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ],
                    [
                        'email' => $merchantEmail ?: 'default_' . $adminName . '@example.com', // Fallback email
                        'phone' => $merchantPhone ?: '',
                        'password' => bcrypt('default_password'), // Default hashed password
                    ]
                );

                $merchantId = null;
                if ($merchantUsername) {
                    $merchant = Merchant::updateOrCreate(
                        [
                            'username' => $merchantUsername,
                        ],
                        [
                            'email' => $merchantEmail,
                            'phone' => $merchantPhone,
                            'password' => $merchantPassword ?: bcrypt('default_password'),
                            'admin_id' => $admin->id,
                        ]
                    );
                    $merchantId = $merchant->id;
                }

                // Gom thiết bị từ all items in group
                $devices = [];
                foreach ($items as $row) {
                    $deviceCode = trim($row[22] ?? '');
                    $deviceName = trim($row[23] ?? '');
                    $pin = (int) ($row[24] ?? 0);

                    if ($deviceCode && $deviceName) {
                        $devices[] = [
                            'code' => $deviceCode,
                            'name' => $deviceName,
                            'pin' => $pin,
                        ];
                    }
                }
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
                        'merchant_id' => $merchantId,
                        'title' => $title,
                        'ceo_sign' => $ceoSign ?: Contract::CURRENT_CEO,
                        'location' => $location,
                        'customer_name' => $customerName,
                        'customer_position' => $customerPosition,
                        'customer_cccd' => $customerCccd,
                        'business_registration' => $businessRegistration,
                    ]
                );

                // Shop
                $region = $city = $area = null;
                if (preg_match('/\((.*?)\)/', $shopName, $matches)) {
                    $parts = explode('-', trim($matches[1]));
                    $region = trim($parts[0] ?? '');
                    $city = trim($parts[1] ?? '');
                    $area = trim($parts[2] ?? '');
                }

                Shop::updateOrCreate(
                    [
                        'shop_name' => $shopName,
                    ],
                    [
                        'contract_id' => $contract->id,
                        'address' => $location,
                        'shop_type' => $shopType,
                        'share_rate' => (float) ($shareRate ?: 0),
                        'share_rate_type' => 'fixed',
                        'contact_phone' => $merchantPhone,
                        'strategy' => '(VND-1h)20-10000-52000',
                        'area' => $area,
                        'city' => $city,
                        'region' => $region,
                        'device_json' => $deviceJson,
                        'merchant_id' => $merchantId,
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
                // Handle formats like d/m/Y or other variations in data
                return Carbon::createFromFormat('d/m/Y', $value) ?? Carbon::parse($value);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
