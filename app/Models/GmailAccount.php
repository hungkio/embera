<?php

namespace App\Models;

use App\Domain\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GmailAccount extends Model
{
    protected $fillable = [
        'admin_id',
        'email',
        'access_token',
        'refresh_token',
        'token_type',
        'scopes',
        'expires_at',
        'last_scanned_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'last_scanned_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GmailMessage::class)->latest('received_at')->latest('id');
    }

    public function isExpired(): bool
    {
        return !$this->expires_at || $this->expires_at->copy()->subMinute()->isPast();
    }
}
