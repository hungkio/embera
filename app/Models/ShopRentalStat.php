<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopRentalStat extends Model
{
    protected $table = 'shop_rental_stats';

    protected $fillable = [
        'shop_id',
        'total_slots',
        'renting_slots',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}
