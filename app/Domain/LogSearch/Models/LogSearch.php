<?php

namespace App\Domain\LogSearch\Models;

use Illuminate\Database\Eloquent\Model;

class LogSearch extends Model
{
    public $guarded = [];
    protected $table = 'log_searchs';
    protected $fillable = ['key_word', 'hits'];
}
