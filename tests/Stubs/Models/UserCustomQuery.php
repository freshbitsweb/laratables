<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserCustomQuery extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Fetch only user with id 1 in the datatables.
     *
     * @param \Illuminate\Database\Eloquent\Builder
     * @param \Illuminate\Database\Eloquent\Builder
     */
    public static function laratablesQueryConditions($query)
    {
        return $query->where('id', 1);
    }
}
