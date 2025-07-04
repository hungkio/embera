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
        'is_deleted' => 'boolean', // Cast is_deleted to boolean
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
        'phone',
        'shop_id',
        'merchant_id',
        'admin_id',
        'title',
        'ceo_sign',
        'location',
        'note',
        'upload',
        'download_count',
        'is_deleted',
    ];

    CONST BBNT = 'Chỉ có BBNT';
    CONST NOT_SIGN = 'Chưa Ký';
    CONST SIGN = 'Đã ký';
    CONST STATUS = [
        self::BBNT => 'Chỉ có BBNT',
        self::NOT_SIGN => 'Chưa Ký',
        self::SIGN => 'Đã ký',
    ];

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
}
