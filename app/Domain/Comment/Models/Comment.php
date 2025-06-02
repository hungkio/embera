<?php

namespace App\Domain\Comment\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $guarded = [];
    
    protected $fillable = [
        'user_id',
        'post_id',
        'news_id',
        'content'
    ];
}
