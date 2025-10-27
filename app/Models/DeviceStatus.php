<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    protected $table = 'device_status';
    protected $fillable = ['equip_id', 'status'];
    public $timestamps = true;
}
