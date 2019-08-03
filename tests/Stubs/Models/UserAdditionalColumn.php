<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserAdditionalColumn extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns remember_token instead of name for the datatables.
     *
     * @param \App\Param
     * @return string
     */
    public static function laratablesName($user)
    {
        return $user->remember_token;
    }

    /**
     * Additional columns to be loaded for datatables.
     *
     * @return array
     */
    public static function laratablesAdditionalColumns()
    {
        return ['remember_token'];
    }
}
