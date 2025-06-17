<?php

namespace App\Domain\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';

    protected $fillable = [
        'customer_id',
        'file_path',
        'contract_no',
        'file_name',
        'devices',
        'uploaded_at',
        'placed_time',
    ];

    protected $dates = ['uploaded_at', 'placed_time', 'created_at', 'updated_at'];
}
