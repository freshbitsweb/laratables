<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Controllers;

use Freshbitsweb\Laratables\Laratables;
use Freshbitsweb\Laratables\Tests\Stubs\Models\User;

class DatatablesController
{
    /**
     * Default method of the controller.
     *
     * @return json
     */
    public function __invoke()
    {
        return Laratables::recordsOf(User::class);
    }
}