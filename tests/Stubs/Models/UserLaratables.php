<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserLaratables extends User
{
    /**
     * Returns the id of the user after prepending leading zeroes.
     *
     * @param \App\User
     * @return string
     */
    public static function laratablesCustomAction($user)
    {
        return "<span>{$user->id}</span>";
    }
}
