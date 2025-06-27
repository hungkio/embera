<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Admin\Models\Admin;

class Merchant extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'username',
        'password',
        'admin_id',
        'is_deleted',
        'upload',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $casts = [
        'is_deleted' => 'boolean',
        'upload' => 'string',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeWithDeleted($query)
    {
        return $query->where('is_deleted', 1);
    }

    public function hasPassword()
    {
        return !empty($this->password);
    }
}
