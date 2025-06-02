<?php

declare(strict_types=1);

namespace App\Domain\MailSetting\Models;

use App\Domain\Model;

class MailSetting extends Model
{
    protected $table = 'mail_settings';
    protected $fillable = ['slug', 'name', 'value'];

}
