<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Admin\Models\Admin;

class MerchantShareLog extends Model
{
    protected $table = 'merchant_share_logs';

    protected $fillable = [
        'merchant_id',
        'year',
        'month',
        'contract_no',
        'customer_name',
        'date',
        'number_of_order',
        'share_percent',
        'total',
        'share_money',
        'type',
        'share_type',
    ];

    // Quan hệ với Merchant
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
