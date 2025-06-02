<?php

namespace App\Domain\SubscribeEmail\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeEmail extends Model
{
    public $guarded = [];
    protected $table = 'subscribe_emails';
    protected $fillable = ['email'];
}
