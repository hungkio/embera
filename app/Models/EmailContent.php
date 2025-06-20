<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailContent extends Model
{
    protected $fillable = ['email_id', 'text'];

    public function email()
    {
        return $this->belongsTo(Email::class);
    }
}
