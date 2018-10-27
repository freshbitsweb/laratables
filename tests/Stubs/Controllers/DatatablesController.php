<?php

namespace Freshbitsweb\Laratables\Tests\Stubs\Controllers;

use Freshbitsweb\Laratables\Laratables;
use Freshbitsweb\Laratables\Tests\Stubs\Models\User;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomName;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserLaratables;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomOrder;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomQuery;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCountryQuery;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserCustomSearch;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserAdditionalColumn;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserModifyCollection;
use Freshbitsweb\Laratables\Tests\Stubs\Models\UserSearchableColumns;

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
        return Laratables::recordsOf(User::class, function ($query) {
            return $query->where('id', 1);
        });
    }

    /**
     * Datatables return with a separate class.
     *
     * @return json
     */
    public function recordsOfClass()
    {
        return Laratables::recordsOf(User::class, UserLaratables::class);
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

    /**
     * Datatables return with a custom search query.
     *
     * @return json
     */
    public function customSearch()
    {
        return Laratables::recordsOf(UserCustomSearch::class);
    }

    /**
     * Datatables return with a custom order query.
     *
     * @return json
     */
    public function customOrder()
    {
        return Laratables::recordsOf(UserCustomOrder::class);
    }

    /**
     * Datatables return with an additional column query.
     *
     * @return json
     */
    public function additionalColumn()
    {
        return Laratables::recordsOf(UserAdditionalColumn::class);
    }

    /**
     * Datatables return with an additional column query.
     *
     * @return json
     */
    public function modifyCollection()
    {
        return Laratables::recordsOf(UserModifyCollection::class);
    }

    /**
     * Datatables return with an searchable column query.
     *
     * @return json
     */
    public function searchableColumns()
    {
        return Laratables::recordsOf(UserSearchableColumns::class);
    }
}
