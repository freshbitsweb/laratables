<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

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
     * @return string
     */
    public function laratablesName()
    {
        return str_limit($this->name, 5);
    }
}
