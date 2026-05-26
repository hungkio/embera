<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTurnOnHistory extends Model
{
    protected $fillable = [
        'recorded_date',
        'equip_id',
        'code',
        'shop_code',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'recorded_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(TblShop::class, 'shop_code', 'code');
    }
}
