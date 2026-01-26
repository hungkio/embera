<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceRental extends Model
{
    protected $fillable = ['equip_id', 'shop_code', 'is_renting'];
    protected $casts = ['is_renting' => 'boolean'];
}
