<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GmailMessage extends Model
{
    protected $fillable = [
        'gmail_account_id',
        'gmail_message_id',
        'thread_id',
        'subject',
        'from_name',
        'from_email',
        'snippet',
        'body_text',
        'body_html',
        'labels',
        'is_unread',
        'received_at',
    ];

    protected $casts = [
        'labels' => 'array',
        'is_unread' => 'boolean',
        'received_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class, 'gmail_account_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(GmailAttachment::class, 'gmail_message_id');
    }
}
