<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblDevice extends Model
{
    protected $table = 'tbl_devices';
    protected $fillable = [
        'code',
        'shop_code',
    ];
}
