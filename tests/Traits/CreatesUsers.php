<?php

namespace Freshbitsweb\Laratables\Tests\Traits;

use Freshbitsweb\Laratables\Tests\Stubs\Models\User;
use Freshbitsweb\Laratables\Tests\Stubs\Models\Country;

trait CreatesUsers
{
    public function createUsers($count = 1)
    {
        factory(Country::class)->create();
        factory(User::class, $count)->create();
    }
}