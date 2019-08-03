<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

use Illuminate\Support\Str;

class UserCustomName extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns truncated name for the datatables.
     *
     * @param \App\User
     * @return string
     */
    public static function laratablesName($user)
    {
        return Str::limit($user->name, 5);
    }
}
