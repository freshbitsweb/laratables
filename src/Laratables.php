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
    protected function __construct($model)
    {
        $this->queryHandler = new QueryHandler($model);
        $this->columnManager = new ColumnManager($model);
        $this->recordsTransformer = new RecordsTransformer($model, $this->columnManager);
    }

    /**
     * Accepts datatables ajax request or Laratables instance and returns table data.
     *
     * @return array Table data
     */
    public static function recordsOf($model)
    {
        if(!($model instanceof static))
            $instance = static::forModel($model);
        else $instance = $model;

        $records = $instance->fetchRecords();
        $records = $instance->recordsTransformer->transformRecords($records);
        return $instance->tableData($records);
    }

    /**
     * Accepts model and returns Laratables instance.
     *
     * @return \Freshbitsweb\Laratables\Laratables
     */
    public static function forModel($model)
    {
        $instance = new static($model);
        $instance->applyFiltersTo();

        return $instance;
    }

    /**
     * Modify the underlying query of a Laratables instance.
     *
     * @param Closure which Accepts and returns Eloquent query
     *
     * @return \Freshbitsweb\Laratables\Laratables
     */
    public function modify($closure)
    {
        $this->queryHandler->modify($closure);
        return $this;
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
            ->offset(request('start'))
            ->limit(request('length'))
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
