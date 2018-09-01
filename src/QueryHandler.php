<?php

namespace Freshbitsweb\Laratables;

class QueryHandler
{
    protected $query;

    protected $recordsCount;

    /**
     * Initialize properties.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->setQuery($model);
        $this->recordsCount = $this->query->count();
    }

    /**
     * Initialises Query object.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     *
     * @return void
     */
    protected function setQuery($model)
    {
        $this->query = new $model();

        if (method_exists($model, 'laratablesQueryConditions')) {
            $this->query = $model::laratablesQueryConditions($this->query);
        }
    }

    /**
     * Applies where conditions to the query according to search value.
     *
     * @param array Columns to be searched
     * @param string Search value
     *
     * @return \Illuminate\Database\Query\Builder Query object
     */
    public function applyFilters($searchColumns, $searchValue)
    {
        $this->query = FilterAgent::applyFiltersTo($this->query, $searchColumns, $searchValue);
    }

    /**
     * Returns the query object.
     *
     * @return int
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
}
