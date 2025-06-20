<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = ['to', 'merchant_id', 'status'];

    public function content()
    {
        return $this->hasOne(EmailContent::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
