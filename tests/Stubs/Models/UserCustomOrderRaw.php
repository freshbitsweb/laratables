<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

class UserCustomOrderRaw extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * name and email column should be used for sorting when id column is selected in Datatables.
     *
     * @param string Direction
     * @return string
     */
    public static function laratablesOrderRawId($direction)
    {
        $otherDirection = $direction == 'asc' ? 'desc' : 'asc';

        return 'name '.$otherDirection.', email '.$direction;
    }
}
