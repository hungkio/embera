<?php


namespace App\Domain;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    protected $guarded = ['id'];
}
