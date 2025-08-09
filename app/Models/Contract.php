<?php

namespace App\Models;

use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';
    protected $casts = [
        'sign_date' => 'date',
        'expired_date' => 'date',
        'title' => 'string',
        'is_deleted' => 'boolean',
    ];
    protected $fillable = [
        'contract_number',
        'sign_date',
        'expired_date',
        'status',
        'expired_time',
        'bank_info',
        'bank_account_number',
        'bank_account_name',
        'email',
        'customer_name',
        'customer_cccd',
        'customer_position',
        'phone',
        'shop_id',
        'merchant_id',
        'admin_id',
        'business_registration',
        'title',
        'ceo_sign',
        'location',
        'city',
        'note',
        'upload',
        'download_count',
        'is_deleted',
    ];

    CONST BBNT = '0';
    CONST NOT_SIGN = '1';
    CONST SIGN = '2';
    CONST STATUS = [
        self::BBNT => 'Chỉ có BBNT',
        self::NOT_SIGN => 'Chưa Ký',
        self::SIGN => 'Đã ký',
    ];

    CONST CURRENT_CEO = "Lê Thị Lý";

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Custom scope to filter non-deleted records
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    // Custom scope to include deleted records
    public function scopeWithDeleted($query)
    {
        return $query->where('is_deleted', 1);
    }

    public static function provinces(): array
    {
        return [
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
    }

    /**
     * Accessor để lấy tên tỉnh dựa vào city_code
     */
    public function getCityNameAttribute(): ?string
    {
        $provinces = self::provinces();

        // nếu bạn lưu city_code là số nguyên, có thể format lại 2 chữ số:
        $code = str_pad((string)$this->city_code, 2, '0', STR_PAD_LEFT);

        return $provinces[$code] ?? null;
    }

    // 1) Mặc định Laravel sẽ only lấy các trường có trong table,
    //    nên chúng ta append thêm thuộc tính ảo 'full_contract_number'
    protected $appends = ['full_contract_number'];

    /**
     * Trả về số hợp đồng có ghép city code lên đầu, zero-pad 5 chữ số.
     */
    public function getFullContractNumberAttribute(): string
    {
        // contract_number trong DB chỉ chứa phần số (ví dụ "00001")
        $suffix   = str_pad($this->contract_number, 5, '0', STR_PAD_LEFT);
        $cityCode = $this->city ?: '';

        return $cityCode . $suffix;  // ví dụ: "29" . "00001" => "2900001"
    }
}
