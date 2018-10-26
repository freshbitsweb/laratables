<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserCustomSearch extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Searches inside the remember_token column instead of a name column.
     *
     * @param \Illuminate\Database\Eloquent\Builder
     * @param string search term
     * @param \Illuminate\Database\Eloquent\Builder
     */
    public static function laratablesSearchName($query, $searchValue)
    {
        return $query->orWhere('remember_token', $searchValue);
    }
}
