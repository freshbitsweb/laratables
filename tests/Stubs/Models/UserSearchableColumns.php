<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserSearchableColumns extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Additonal searchable columns to be used for datatables.
     *
     * @return array
     */
    public static function laratablesSearchableColumns()
    {
        return ['remember_token'];
    }
}
