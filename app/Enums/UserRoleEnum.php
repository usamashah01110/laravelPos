<?php

namespace App\Enums;

use Rexlabs\Enum\Enum;

/**
 * The UserRoleEnum enum.
 *
 * @method static self ADMIN()
 * @method static self SUBSCRIBER()
 */
class UserRoleEnum extends Enum
{
//    const ADMIN = 1;
//    const SUBSCRIBER = 2;

    /**
     * Retrieve a map of enum keys and values.
     *
     * @return array
     */
    public static function map() : array
    {
        return [
//            static::ADMIN => 'Admin',
//            static::SUBSCRIBER => 'Subscriber',
        ];
    }
}
