<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Returns the id of the user after prepending leading zeroes.
     *
     * @param \App\User
     * @return string
     */
    public static function laratablesCustomAction($user)
    {
        return "<a>{$user->id}</a>";
    }
}
