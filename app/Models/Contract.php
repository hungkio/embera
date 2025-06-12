<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contracts';
    protected $casts = [
        'sign_date' => 'date',
        'expired_date' => 'date',
        'expired_time' => 'integer',
        'title' => 'string',
        'is_deleted' => 'boolean', // Cast is_deleted to boolean
    ];
    protected $fillable = [
        'contract_number',
        'sign_date',
        'expired_date',
        'status',
        'expired_time',
        'bank_info',
        'email',
        'phone',
        'admin_id',
        'title',
        'ceo_sign',
        'location',
        'note',
        'upload',
        'download_count',
        'is_deleted', // Add to fillable if you want to set it manually
    ];

    // Custom scope to filter non-deleted records
    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    // Custom scope to include deleted records
    public function scopeWithDeleted($query)
    {
        return $query->where('is_deleted', 1);
    }
}
