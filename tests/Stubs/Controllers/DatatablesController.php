<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Controllers;

use Freshbitsweb\Laratables\Laratables;
use Freshbitsweb\Laratables\Tests\Stubs\Models\User;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomName;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomQuery;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCountryQuery;

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
     * Datatables return with closure.
     *
     * @return json
     */
    public function recordsOfClosure()
    {
        return Laratables::recordsOf(User::class, function($query) {
            return $query->where('id', 1);
        });
    }

    /**
     * Datatables return with a customized column.
     *
     * @return json
     */
    public function customizeColumn()
    {
        return Laratables::recordsOf(UserCustomName::class);
    }

    /**
     * Datatables return with a custom query.
     *
     * @return json
     */
    public function customQuery()
    {
        return Laratables::recordsOf(UserCustomQuery::class);
    }

    /**
     * Datatables return with a custom relation query.
     *
     * @return json
     */
    public function customRelationQuery()
    {
        return Laratables::recordsOf(UserCountryQuery::class);
    }
}