<?php

namespace App\Enums;

use Rexlabs\Enum\Enum;

/**
 * The Status enum.
 *
 * @method static self PENDING()
 * @method static self ACTIVE()
 * @method static self WAIT()
 */
class UserStatusEnum extends Enum
{
    const PENDING = 1;
    const ACTIVE = 2;
    const WAITING = 3;

    /**
     * Retrieve a map of enum keys and values.
     *
     * @return array
     */
    public static function map() : array
    {
        return [
            static::PENDING => 'Pending',
            static::ACTIVE => 'Active',
            static::WAITING => 'Waiting',
        ];
    }
}
