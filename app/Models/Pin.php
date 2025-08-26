<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $table = 'pins';
    protected $fillable = ['imei', 'serial_number','is_deleted'];
    protected $dates = ['deleted_at'];
    public $timestamps = true;
}
