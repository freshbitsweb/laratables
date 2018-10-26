<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserCountryQuery extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns the code of the country as it's name.
     *
     * @return callable
     */
    public static function laratablesCountryRelationQuery()
    {
        return function ($query) {
            $query->select('id', 'code as name');
        };
    }
}
