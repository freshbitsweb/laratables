<?php

namespace Freshbitsweb\Laratables\Tests\Traits;

use Freshbitsweb\Laratables\Tests\Stubs\Models\User;
use Freshbitsweb\Laratables\Tests\Stubs\Models\Country;

trait CreatesUsers
{
    /**
     * Seeds user(s) in the database.
     *
     * @return mixed
     */
    protected function createUsers($count = 1)
    {
        factory(Country::class)->create();

        return factory(User::class, $count)->create();
    }
}