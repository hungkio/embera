<?php

namespace App\Imports;

use App\Domain\Admin\Models\Admin;
use App\Models\Contract;
use App\Models\Merchant;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ContractImport implements ToCollection, WithCalculatedFormulas
{
    /**
     * Map code số → tên đầy đủ
     */
    private array $cityMap = [
        '01' => 'Thành phố Hà Nội',
        '04' => 'Cao Bằng',
        '08' => 'Tuyên Quang',
        '11' => 'Điện Biên',
        '12' => 'Lai Châu',
        '14' => 'Sơn La',
        '15' => 'Lào Cai',
        '19' => 'Thái Nguyên',
        '20' => 'Lạng Sơn',
        '22' => 'Quảng Ninh',
        '24' => 'Bắc Ninh',
        '25' => 'Phú Thọ',
        '31' => 'Thành phố Hải Phòng',
        '33' => 'Hưng Yên',
        '37' => 'Ninh Bình',
        '38' => 'Thanh Hóa',
        '40' => 'Nghệ An',
        '42' => 'Hà Tĩnh',
        '44' => 'Quảng Trị',
        '46' => 'Thành phố Huế',
        '48' => 'Thành phố Đà Nẵng',
        '51' => 'Quảng Ngãi',
        '52' => 'Gia Lai',
        '56' => 'Khánh Hòa',
        '66' => 'Đắk Lắk',
        '68' => 'Lâm Đồng',
        '75' => 'Đồng Nai',
        '79' => 'Thành phố Hồ Chí Minh',
        '80' => 'Tây Ninh',
        '82' => 'Đồng Tháp',
        '86' => 'Vĩnh Long',
        '91' => 'An Giang',
        '92' => 'Thành phố Cần Thơ',
        '96' => 'Cà Mau',
    ];

    /**
     * Map viết tắt → code số (sửa lặp 'TN')
     */
    private array $abbrToCode = [
        'HN'   => '01', // Hà Nội
        'CB'   => '04', // Cao Bằng
        'TQ'   => '08', // Tuyên Quang
        'ĐB'   => '11', // Điện Biên
        'LC'   => '12', // Lai Châu
        'SL'   => '14', // Sơn La
        'LCA'  => '15', // Lào Cai
        'TNG'  => '19', // Thái Nguyên (đổi từ 'TN' để tránh lặp)
        'LS'   => '20', // Lạng Sơn
        'QN'   => '22', // Quảng Ninh
        'BN'   => '24', // Bắc Ninh
        'PT'   => '25', // Phú Thọ
        'HP'   => '31', // Hải Phòng
        'HY'   => '33', // Hưng Yên
        'NB'   => '37', // Ninh Bình
        'TH'   => '38', // Thanh Hóa
        'NA'   => '40', // Nghệ An
        'HT'   => '42', // Hà Tĩnh
        'QT'   => '44', // Quảng Trị
        'HUE'  => '46', // Huế
        'DN'   => '48', // Đà Nẵng
        'QNG'  => '51', // Quảng Ngãi
        'GL'   => '52', // Gia Lai
        'KH'   => '56', // Khánh Hòa
        'DL'   => '66', // Đắk Lắk
        'LD'   => '68', // Lâm Đồng
        'ĐN'   => '75', // Đồng Nai
        'HCM'  => '79', // TP HCM
        'TNI'  => '80', // Tây Ninh (đổi từ 'TN')
        'ĐT'   => '82', // Đồng Tháp
        'VL'   => '86', // Vĩnh Long
        'AG'   => '91', // An Giang
        'CT'   => '92', // Cần Thơ
        'CM'   => '96', // Cà Mau
    ];

    public function collection(Collection $rows)
    {
        $statuses = [
            'đã ký'   => 2,
            'chưa ký' => 1,
            'không ký'=> 1,
        ];

        try {
            $grouped = $rows->groupBy(fn($row) => trim($row[0] ?? ''));

            if ($grouped->has('shop name')) {
                $grouped = $grouped->forget('shop name');
            }

            foreach ($grouped as $groupKey => $items) {
                if ($items->isEmpty()) {
                    continue;
                }

                DB::transaction(function () use ($items, $statuses) {
                    $firstRow = $items->first();

                    $shopName        = trim($firstRow[0] ?? '');
                    $contractNumber  = trim($firstRow[1] ?? '');
                    $signDate        = $this->parseDate($firstRow[2] ?? null);
                    $expiredDate     = $this->parseDate($firstRow[3] ?? null);
                    $statusRaw       = strtolower(trim($firstRow[4] ?? ''));
                    $status          = $statuses[$statusRaw] ?? 1;
                    $bankInfo        = trim($firstRow[5] ?? '');
                    $bankNumber      = trim($firstRow[6] ?? '');
                    $bankName        = trim($firstRow[7] ?? '');
                    $bdName          = trim($firstRow[8] ?? '');
                    $customerName    = trim($firstRow[9] ?? '');
                    $customerPos     = trim($firstRow[10] ?? '');
                    $merchantEmail = trim($firstRow[11] ?? '');
                    // Nếu rỗng, hoặc bằng "Không sử dụng", hoặc không hợp lệ → để null
                    if (
                        !$merchantEmail ||
                        mb_strtolower($merchantEmail) === 'không sử dụng' ||
                        !filter_var($merchantEmail, FILTER_VALIDATE_EMAIL)
                    ) {
                        $merchantEmail = ''; // hoặc NULL
                    }

                    $merchantPhone   = trim($firstRow[12] ?? '');
                    $businessReg     = trim($firstRow[13] ?? '');
                    $customerCccd    = trim($firstRow[14] ?? '');
                    $location        = trim($firstRow[15] ?? '');
                    $title           = trim($firstRow[17] ?? '');
                    $ceoSign         = trim($firstRow[18] ?? '');
                    $shopType        = trim($firstRow[19] ?? '');
                    $merchantName    = trim($firstRow[20] ?? '');
                    $shareRate       = trim($firstRow[21] ?? '');
                    $merchantUsername= trim($firstRow[25] ?? '');
                    $merchantPassword= trim($firstRow[26] ?? '');

                    if (!$shopName || !$contractNumber) {
                        Log::warning("Skipping group for shop/contract: '{$shopName}' / '{$contractNumber}' due to missing required fields.");
                        return;
                    }

                    $adminName = $bdName ?: $customerPos ?: $customerName;  // Fallback thêm $customerName để giảm skip
                    if (!$adminName) {
                        Log::warning("Skipping row for shop '{$shopName}' do thiếu BD name/customer info.", ['row' => $firstRow->toArray()]);
                        return;
                    }

                    $nameParts = explode(' ', $adminName);
                    $firstName = array_shift($nameParts) ?? '';
                    $lastName  = implode(' ', $nameParts) ?? '';

                    if (!$firstName || !$lastName) {
                        Log::warning("Skipping row for shop '{$shopName}' due to invalid admin name format.");
                        return;
                    }

                    // Fix duplicate email: Kiểm tra email trước
                    $email = $merchantEmail ?: 'default_' . uniqid() . '@example.com';
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $email = 'invalid_' . uniqid() . '@example.com';  // Tránh email invalid như 'Không sử dụng'
                    }

                    // Tìm admin hiện có bằng tên
                    $existingAdmin = Admin::where('first_name', $firstName)
                                          ->where('last_name', $lastName)
                                          ->first();

                    if ($existingAdmin) {
                        // Nếu tồn tại, update nếu cần, nhưng tránh duplicate email
                        if ($email !== $existingAdmin->email && Admin::where('email', $email)->exists()) {
                            $email = 'duplicate_fix_' . uniqid() . '@example.com';
                        }
                        $existingAdmin->update([
                            'email'    => $email,
                            'phone'    => $merchantPhone ?: $existingAdmin->phone,
                            'password' => $existingAdmin->password,  // Giữ password cũ trừ khi cần reset
                        ]);
                        $admin = $existingAdmin;
                    } else {
                        // Tạo mới, kiểm tra email unique
                        if (Admin::where('email', $email)->exists()) {
                            $email = 'duplicate_fix_' . uniqid() . '@example.com';
                        }
                        $admin = Admin::create([
                            'first_name' => $firstName,
                            'last_name'  => $lastName,
                            'email'      => $email,
                            'phone'      => $merchantPhone ?: '',
                            'password'   => bcrypt('default_password'),
                        ]);
                    }

                    $merchantId = null;
                    if ($merchantUsername) {
                        // Kiểm tra merchant tồn tại
                        $existingMerchant = Merchant::where('username', $merchantUsername)->first();
                        if ($existingMerchant) {
                            $existingMerchant->update([
                                'email'    => $merchantEmail ?: $existingMerchant->email,
                                'phone'    => $merchantPhone ?: $existingMerchant->phone,
                                'password' => $merchantPassword ?: $existingMerchant->password,
                                'admin_id' => $admin->id,
                            ]);
                            $merchantId = $existingMerchant->id;
                        } else {
                            $merchant = Merchant::create([
                                'username' => $merchantUsername,
                                'email'    => $merchantEmail,
                                'phone'    => $merchantPhone,
                                'password' => bcrypt($merchantPassword ?: 'default_password'),
                                'admin_id' => $admin->id,
                            ]);
                            $merchantId = $merchant->id;
                        }
                    }

                    $devices  = [];
                    $maxPins  = 0;
                    foreach ($items as $row) {
                        $deviceCode = trim($row[22] ?? '');
                        $deviceName = trim($row[23] ?? '');
                        $pin        = (int)($row[24] ?? 0);

                        if ($deviceCode && $deviceName) {
                            $devices[] = [
                                'code' => $deviceCode,
                                'name' => $deviceName,
                                'pin'  => $pin,
                            ];
                            $maxPins += $pin;
                        }
                    }

                    $expiredTime = null;
                    if ($signDate && $expiredDate) {
                        $expiredTime = $signDate->diffInMonths($expiredDate) . ' tháng';
                    }

                    // Parse region/city/area từ shopName
                    $region = $city = $area = null;
                    if (preg_match('/\((.*?)\)/', $shopName, $matches)) {
                        $parts  = explode('-', trim($matches[1]));
                        $region = trim($parts[0] ?? '');
                        $abbr   = strtoupper(trim($parts[1] ?? ''));
                        $area   = trim($parts[2] ?? '');

                        if (isset($this->abbrToCode[$abbr])) {
                            $city = $this->abbrToCode[$abbr]; // code số
                        }
                    }

                    // Update or create contract
                    $contract = Contract::updateOrCreate(
                        ['contract_number' => $contractNumber],
                        [
                            'sign_date'            => $signDate,
                            'expired_date'         => $expiredDate,
                            'status'               => $status,
                            'expired_time'         => $expiredTime,
                            'bank_info'            => $bankInfo,
                            'bank_account_number'  => $bankNumber,
                            'bank_account_name'    => $bankName,
                            'email'                => $merchantEmail,
                            'phone'                => $merchantPhone,
                            'admin_id'             => $admin->id,
                            'merchant_id'          => $merchantId,
                            'title'                => $title,
                            'ceo_sign'             => $ceoSign ?: Contract::CURRENT_CEO,
                            'location'             => $location,
                            'customer_name'        => $customerName,
                            'customer_position'    => $customerPos,
                            'customer_cccd'        => $customerCccd,
                            'business_registration'=> $businessReg,
                            'city'                 => $city, // Lưu code số vào DB
                        ]
                    );

                    // Update or create shop
                    Shop::updateOrCreate(
                        ['shop_name' => $shopName],
                        [
                            'contract_id'    => $contract->id,
                            'address'        => $location,
                            'shop_type'      => $shopType,
                            'share_rate'     => (float)($shareRate ?: 0),
                            'share_rate_type'=> 'fixed',
                            'contact_phone'  => $merchantPhone,
                            'strategy'       => '(VND-1h)20-10000-52000',
                            'area'           => $area,
                            'city'           => $city,
                            'region'         => $region,
                            'device_json'    => [
                                'devices'   => $devices,
                                'device_id' => $contractNumber . '_' . $shopName,
                                'max_pins'  => $maxPins,
                            ],
                            'merchant_id'    => $merchantId,
                            'is_deleted'     => 0,
                        ]
                    );

                    Log::info("Successfully processed shop: {$shopName}");
                }, 5);  // Tăng attempts để retry deadlock
            }
        } catch (\Illuminate\Database\DeadlockException $e) {
            Log::error('Deadlock during import: ' . $e->getMessage() . '. Consider retrying the import.');
            throw $e;
        } catch (\Exception $e) {
            Log::error('Contract Import Error: ' . $e->getMessage() . ' - Line: ' . $e->getLine());
            throw $e;
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return Carbon::parse($date);
            } else {
                return Carbon::createFromFormat('d/m/Y', $value) ?: Carbon::parse($value);
            }
        } catch (\Exception $e) {
            Log::warning('Date parse error: ' . $e->getMessage());
            return null;
        }
    }
}
