<?php

namespace Freshbitsweb\Laratables;

class Laratables
{
    protected $queryHandler;

    protected $columnManager;

    protected $recordsTransformer;

    /**
     * Declare objects.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     *
     * @return void
     */
    protected function __construct($model, $query = null)
    {
        $this->queryHandler = new QueryHandler($model, $query);
        $this->columnManager = new ColumnManager($model);
        $this->recordsTransformer = new RecordsTransformer($model, $this->columnManager);
    }

    /**
     * Accepts datatables ajax request and returns table data.
     *
     * @param Model to query for
     * @param (optional) Closure accepts and returns the Eloquent query builder
     *
     * @return array Table data
     */
    public static function recordsOf($model, $query = null)
    {
        $instance = new static($model, $query);

        $instance->applyFiltersTo();

        $records = $instance->fetchRecords();

        $records = $instance->recordsTransformer->transformRecords($records);

        return $instance->tableData($records);
    }

    /**
     * Applies conditions to the query if search is performed in datatables.
     *
     * @return void
     */
    protected function applyFiltersTo()
    {
        $searchValue = request('search')['value'];

        if ($searchValue) {
            $this->queryHandler->applyFilters($this->columnManager->getSearchColumns(), $searchValue);
        }
    }

    /**
     * Fetches records from the database.
     *
     * @return \Illuminate\Support\Collection Records of the table
     */
    protected function fetchRecords()
    {
        $query = $this->queryHandler->getQuery();

        return $query->with($this->columnManager->getRelations())
            ->offset((int) request('start'))
            ->limit((int) request('length'))
            ->orderBy(...$this->columnManager->getOrderBy())
            ->get($this->columnManager->getSelectColumns());
    }

    /**
     * Prepares and returns data for the datatables.
     *
     * @param \Illuminate\Support\Collection Records of the table
     *
     * @return array
     */
    protected function tableData($records)
    {
        return [
            'draw'            => request('draw') + 1,
            'recordsTotal'    => $this->queryHandler->getRecordsCount(),
            'recordsFiltered' => $this->queryHandler->getFilteredCount(),
            'data'            => $records->toArray(),
        ];
    }
}
