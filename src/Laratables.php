<?php

namespace Freshbitsweb\Laratables;

class Laratables
{
    protected $queryHandler;

    protected $columnManager;

    protected $filterAgent;

    /**
     * Declare objects
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    protected function __construct($model)
    {
        $this->queryHandler = new QueryHandler($model);
        $this->columnManager = new ColumnManager($model);
        $this->filterAgent = new FilterAgent($model);
        $this->recordsTransformer = new RecordsTransformer($model, $this->columnManager);
    }

    /**
     * Accepts datatables ajax request and returns table data
     *
     * @return array Table data
     */
    public static function recordsOf($model)
    {
        $instance = new static($model);

        $instance->applyFiltersTo();

        $records = $instance->fetchRecords();

        $records = $instance->recordsTransformer->transformRecords($records);

        return $instance->tableData($records);
    }

    /**
     * Applies conditions to the query if search is performed in datatables
     *
     * @return void
     */
    protected function applyFiltersTo()
    {
        $searchValue = request('search')['value'];

        if ($searchValue) {
            $this->queryHandler = $this->filterAgent->applyFiltersTo($this->queryHandler, $this->columnManager->getSearchColumns(), $searchValue);
        }
    }

    /**
     * Fetches records from the database
     *
     * @return \Illuminate\Support\Collection Records of the table
     */
    protected function fetchRecords()
    {
        $query = $this->queryHandler->getQuery();

        return $query->with($this->columnManager->getRelations())
            ->offset(request('start'))
            ->limit(request('length'))
            ->orderBy(...$this->getOrderBy())
            ->get($this->columnManager->getSelectColumns())
        ;
    }

    /**
     * Returns the values for order by clause of the query
     *
     * @return array
     */
    protected function getOrderBy()
    {
        $requestedColumnNames = $this->columnManager->getRequestedColumnNames()->toArray();
        $order = request('order');

        return [$requestedColumnNames[$order[0]['column']], $order[0]['dir']];
    }

    /**
     * Prepares and returns data for the datatables
     *
     * @param \Illuminate\Support\Collection Records of the table
     * @return array
     */
    protected function tableData($records)
    {
        return [
            'draw' => request('draw') + 1,
            'recordsTotal' => $this->queryHandler->getRecordsCount(),
            'recordsFiltered' => $this->queryHandler->getFilteredCount(),
            'data' => $records->toArray(),
        ];
    }
}
