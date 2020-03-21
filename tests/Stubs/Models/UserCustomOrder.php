<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserCustomOrder extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    public static function laratablesCustomUsername($user): string
    {
        return $user->name;
    }

    public static function laratablesOrderUsername(): string
    {
        return 'name';
    }
}
