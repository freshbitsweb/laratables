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

    /**
     * name column should be used for sorting when id column is selected in Datatables.
     *
     * @return string
     */
    public static function laratablesOrderId()
    {
        return 'name';
    }
}
