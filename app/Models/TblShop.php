<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblShop extends Model
{
    protected $table = 'tbl_shops';
    protected $fillable = [
        'code',
        'name',
    ];

    public function contractShop()
    {
        return $this->hasOne(Shop::class, 'shop_name', 'name');
    }

    public function devices()
    {
        return $this->hasMany(DeviceStatus::class, 'shop_code', 'code');
    }
}
