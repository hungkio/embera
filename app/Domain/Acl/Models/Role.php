<?php

declare(strict_types=1);

namespace App\Domain\Acl\Models;

use App\Support\Traits\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Spatie\Permission\Models\Role as Model;

class Role extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['display_name'];
}
