<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Controllers;

use Freshbitsweb\Laratables\Laratables;
use Freshbitsweb\Laratables\Tests\Stubs\Models\User;

class DatatablesController
{
    /**
     * Simple datatables return.
     *
     * @return json
     */
    public function simple()
    {
        return Laratables::recordsOf(User::class);
    }

    /**
     * Satatables return with closure.
     *
     * @return json
     */
    public function recordsOfClosure()
    {
        return Laratables::recordsOf(User::class, function($query) {
            return $query->where('id', 1);
        });
    }
}