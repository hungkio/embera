<?php

declare(strict_types=1);

namespace App\Domain\Acl\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Spatie\Permission\Models\Permission as Model;

class Permission extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['display_name'];
}
