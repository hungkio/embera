<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GmailAttachment extends Model
{
    protected $fillable = [
        'gmail_message_id',
        'filename',
        'mime_type',
        'gmail_attachment_id',
        'storage_disk',
        'storage_path',
        'size',
        'downloaded_at',
        'imported_at',
        'import_status',
        'import_error',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(GmailMessage::class, 'gmail_message_id');
    }
}
