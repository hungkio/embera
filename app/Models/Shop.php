<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'shop_name',
        'address',
        'shop_type',
        'contact_phone',
        'strategy',
        'area',
        'city',
        'region',
        'merchant_id',
        'contract_id',
        'is_deleted',
        'share_rate',
        'share_rate_type',
        'is_bound',
        'device_json',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'device_json' => 'array',
        'share_rate' => 'float',
        'share_rate_type' => 'string',
        'is_bound' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'rental_shop', 'shop_name');
    }
}
