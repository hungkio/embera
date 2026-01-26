<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLocation extends Model
{
    protected $table = 'shop_locations';

    protected $fillable = [
        'shop_id',
        'shop_name',
        'lat',
        'lng',
        'active',
    ];

    protected $casts = [
        'lat'    => 'float',
        'lng'    => 'float',
        'active' => 'boolean',
    ];
}
