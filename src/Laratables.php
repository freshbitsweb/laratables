<?php

namespace Freshbitsweb\Laratables;

class Laratables
{
    /**
     * @var QueryHandler object
     */
    protected $queryHandler;

    /**
     * @var ColumnManager object
     */
    protected $columnManager;

    /**
     * @var RecordsTransformer object
     */
    protected $recordsTransformer;

    /**
     * Declare objects.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @param Class to customize query/data/logic
     * @param callable A closure to customize the query (optional)
     * @return void
     */
    protected function __construct($model, $class, $callable)
    {
        $this->queryHandler = new QueryHandler($model, $class, $callable);
        $this->columnManager = new ColumnManager($model, $class);
        $this->recordsTransformer = new RecordsTransformer($class, $this->columnManager);
    }

    /**
     * Accepts datatables ajax request and returns table data.
     *
     * @param Model to query for
     * @param mixed Class/Callable to customize query/data/logic (optional)
     * @return array Table data
     */
    public static function recordsOf($model, $classOrCallable = null)
    {
        $instance = new static(...self::prepareProperties($model, $classOrCallable));

        $instance->applyFiltersTo();

        $records = $instance->fetchRecords();

        $records = $instance->recordsTransformer->transformRecords($records);

        return $instance->tableData($records);
    }

    /**
     * Prepares the model, class, and callable for the instance.
     *
     * @param Model to query for
     * @param mixed Class/Callable to customize query/data/logic (optional)
     * @return array Model, class, and callable
     */
    private static function prepareProperties($model, $classOrCallable)
    {
        $callable = null;
        $class = $model;

        if (is_object($classOrCallable) && $classOrCallable instanceof \Closure) {
            $callable = $classOrCallable;
        } elseif (is_string($classOrCallable)) {
            $class = $classOrCallable;
        }

        return [$model, $class, $callable];
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

        $query = $query->with($this->columnManager->getRelations())
            ->when($this->shouldApplyLimit(), function ($query) {
                $limit = getRecordsLimit((int) request('length'));

                $query->limit($limit)->offset((int) request('start'));
            })
        ;

        $this->columnManager
            ->getOrderColumns()
            ->each(function ($order) use ($query) {
                if (is_string($order)) {
                    $query = $query->orderByRaw($order);
                } else {
                    $query = $query->orderBy(...$order);
                }
            })
        ;

        return $query->get($this->columnManager->getSelectColumns());
    }

    /**
     * Returns whether limit should be applied on the number of records.
     *
     * @return bool
     **/
    private function shouldApplyLimit()
    {
        return
            // If max_limit is more than 0, we should apply the limit.
            config('laratables.max_limit') > 0 ||
            // If request has a numeric limit, we should apply it.
            (is_numeric(request('length')) && (int) request('length') > 0)
        ;
    }

    /**
     * Prepares and returns data for the datatables.
     *
     * @param \Illuminate\Support\Collection Records of the table
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
