<?php

namespace App\Domain\Tag\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $guarded = [];

    protected $fillable = ['tag','tag_slug'];
}
