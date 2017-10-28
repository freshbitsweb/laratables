<?php

namespace Freshbitsweb\Laratables;

class FilterAgent
{
    protected $model;

    /**
     * Initialize properties
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Applies where conditions to the query according to search value
     *
     * @param \Freshbitsweb\Laratables\Query Query object
     * @param array Columns to be searched
     * @return void
     */
    protected function applyFiltersTo($query, $searchColumns, $searchValue)
    {
        $query = $query->where(function ($query) use ($searchColumns, $searchValue) {
            foreach ($searchColumns as $columnName) {
                $query = $this->applyFilter($query, $columnName, $searchValue);
            }
        });

        $query->setFilteredCount();
    }

    /**
     * Applies filter condition for the table column
     *
     * @param \Freshbitsweb\Laratables\Query Query object
     * @param string Column name
     * @param string Search string
     * @return \Freshbitsweb\Laratables\Query Query object
     */
    protected function applyFilter($query, $column, $searchValue)
    {
        if ($methodName = $this->hasCustomSearch($column)) {
            return $this->model::$methodName($query, $searchValue);
        }

        if ($this->isRelationColumn($column)) {
            return $this->applyRelationFilter($query, $column, $searchValue);
        }

        $searchValue = '%'.$searchValue.'%';
        return $query->orWhere($column, 'like', "$searchValue");
    }

    /**
     * Decides whether column has custom search method defined in the model and returns method name if yes
     *
     * @param string Name of the column
     * @return boolean|string
     */
    protected function hasCustomSearch($columnName)
    {
        $methodName = camel_case('datatables_search_' . $columnName);

        if (method_exists($this->model, $methodName)) {
            return $methodName;
        }

        return false;
    }
}
