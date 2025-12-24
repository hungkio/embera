<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    protected $table = 'tbl_devices';
    protected $fillable = ['code', 'status'];
    public $timestamps = true;

    public function shop()
    {
        return $this->belongsTo(TblShop::class, 'shop_code', 'code');
    }
}
