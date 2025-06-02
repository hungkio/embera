<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Pending()
 * @method static static Active()
 * @method static static Hide()
 * @method static static Disabled()
 */
final class AnalyticsState extends Enum
{
    const Pending = 'pending';
    const Active = 'active';
    const Disabled = 'disabled';

    const SHOW = 1;
    const HIDE = 0;
    const ANALYTIC_MODE = [
        self::SHOW => 'Hiển thị',
        self::HIDE => 'Ẩn',
    ];
}
