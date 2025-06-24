<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MBTransaction extends Model
{
    protected $table = 'mb_transactions';
    protected $fillable = [
        'code_in', 'date_in', 'ft_code_in', 'amount_in',
        'code_out', 'date_out', 'ft_code_out', 'amount_out',
        'revenue'
    ];

    protected $casts = [
        'date_in' => 'datetime',
        'date_out' => 'datetime',
        'amount_in' => 'float',
        'amount_out' => 'float',
        'revenue' => 'float',
    ];
}
