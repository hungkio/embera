<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Pending()
 * @method static static Active()
 * @method static static Hide()
 * @method static static Disabled()
 */
final class PostState extends Enum
{
    const Pending = 'pending';
    const Active = 'active';
    const Disabled = 'disabled';
}
