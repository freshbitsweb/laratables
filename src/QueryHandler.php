<?php

namespace Freshbitsweb\Laratables;

class QueryHandler
{
    /**
     * @var string Class with laratables methods
     */
    protected $class;

    /**
     * @var Eloquent model object query
     */
    protected $query;

    /**
     * @var int Count of the total records available
     */
    protected $recordsCount;

    /**
     * @var int Count of the records after applying search filter
     */
    protected $filteredCount;

    /**
     * Initialize properties.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @param Class to customize query/data/logic
     * @param callable A closure to customize the query (optional)
     * @return void
     */
    public function __construct($model, $class, $callable)
    {
        $this->class = $class;

        $this->setQuery($model, $class);

        if (is_object($callable) && $callable instanceof \Closure) {
            $this->query = $callable($this->query);
        }

        $this->recordsCount = $this->filteredCount = $this->query->count();
    }

    /**
     * Initializes Query object.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @param Class to customize query/data/logic
     * @return void
     */
    protected function setQuery($model, $class)
    {
        $this->query = new $model();

        if (method_exists($class, 'laratablesQueryConditions')) {
            $this->query = $class::laratablesQueryConditions($this->query);
        }
    }

    /**
     * Applies where conditions to the query according to search value.
     *
     * @param array Columns to be searched
     * @param string Search value
     * @return void
     */
    public function applyFilters($searchColumns, $searchValue)
    {
        $this->query = FilterAgent::applyFiltersTo($this->class, $this->query, $searchColumns, $searchValue);
        $this->filteredCount = $this->query->count();
    }

    /**
     * Returns the query object.
     *
     * @return \Illuminate\Database\Query\Builder Query object
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns total records of the table.
     *
     * @return int
     */
    public function getRecordsCount()
    {
        return $this->recordsCount;
    }

    /**
     * Returns total records of the table.
     *
     * @return int
     */
    public function getFilteredCount()
    {
        return $this->filteredCount;
    }
}
