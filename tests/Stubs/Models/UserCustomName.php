<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Models;

use Illuminate\Support\Str;

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
        return Str::limit($this->name, 5);
    }
}
