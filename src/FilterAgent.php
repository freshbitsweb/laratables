<?php

namespace Freshbitsweb\Laratables;

use Illuminate\Support\Str;

class FilterAgent
{
    /**
     * @var string Class with laratables methods
     */
    private static $class;

    /**
     * Applies where conditions to the query according to search value.
     *
     * @param Class to customize query/data/logic
     * @param \Illuminate\Database\Query\Builder Query object
     * @param array Columns to be searched
     * @param string Search value
     * @return \Illuminate\Database\Query\Builder Query object
     */
    public static function applyFiltersTo($class, $query, $searchColumns, $searchValue)
    {
        self::$class = $class;

        return $query->where(function ($query) use ($searchColumns, $searchValue) {
            foreach ($searchColumns as $columnName) {
                $query = self::applyFilter($query, $columnName, $searchValue);
            }
        });
    }

    /**
     * Applies filter condition for the table column.
     *
     * @param \Freshbitsweb\Laratables\QueryHandler Query object
     * @param string Column name
     * @param string Search string
     * @return \Freshbitsweb\Laratables\QueryHandler Query object
     */
    protected static function applyFilter($query, $column, $searchValue)
    {
        if ($methodName = self::hasCustomSearch($column)) {
            return self::$class::$methodName($query, $searchValue);
        }

        if (isRelationColumn($column)) {
            return self::applyRelationFilter($query, $column, $searchValue);
        }

        $searchValue = '%'.$searchValue.'%';

        return $query->orWhere($column, 'like', "$searchValue");
    }

    /**
     * Decides whether column has custom search method defined in the model and returns method name if yes.
     *
     * @param string Name of the column
     * @return bool|string
     */
    protected static function hasCustomSearch($columnName)
    {
        $methodName = Str::camel('laratables_search_'.$columnName);

        if (method_exists(self::$class, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Applies filter condition for the relation column.
     *
     * @param \Freshbitsweb\Laratables\QueryHandler Query object
     * @param string Column name
     * @param string Search string
     * @return \Freshbitsweb\Laratables\QueryHandler Query object
     */
    protected static function applyRelationFilter($query, $column, $searchValue)
    {
        if ($methodName = self::hasCustomSearch(str_replace('.', '_', $column))) {
            return self::$class::$methodName($query, $searchValue);
        }

        [$relationName, $relationColumnName] = getRelationDetails($column);
        $searchValue = '%'.$searchValue.'%';

        return $query->orWhereHas($relationName, function ($query) use ($relationColumnName, $searchValue) {
            $query->where($relationColumnName, 'like', "$searchValue");
        });
    }
}
