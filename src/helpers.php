<?php

use Freshbitsweb\Laratables\Exceptions\InvalidMaxLimit;

if (! function_exists('isRelationColumn')) {
    /**
     * Decides whether specified column is a relation table column.
     *
     * @param string Name of the column
     * @return bool
     */
    function isRelationColumn($columnName)
    {
        return strpos($columnName, '.') !== false;
    }
}

if (! function_exists('getRelationDetails')) {
    /**
     * Returns the relation details from the specified column.
     *
     * @param string Name of the column
     * @return array
     */
    function getRelationDetails($columnName)
    {
        $relationName = strtok($columnName, '.');
        $relationColumnName = strtok('.');

        return [$relationName, $relationColumnName];
    }
}

if (! function_exists('getRelationName')) {
    /**
     * Returns the name of the relation for the column specified.
     *
     * @param string Name of the column
     * @return string
     */
    function getRelationName($columnName)
    {
        [$relationName, $relationColumnName] = getRelationDetails($columnName);

        return $relationName;
    }
}

if (! function_exists('getRecordsLimit')) {
    /**
     * Returns the limit of the records to be fetched from the table.
     *
     * @param int Limit requested by the datatables
     * @return int Limit to be applied in the query
     */
    function getRecordsLimit($requestedLimit)
    {
        $maxLimit = config('laratables.max_limit');

        if (! is_int($maxLimit) || $maxLimit < 0) {
            throw new InvalidMaxLimit("Please set the 'max_limit' configuration parameter to be 0 or more.");
        }

        if ($maxLimit === 0) {
            return $requestedLimit;
        }

        if ($requestedLimit > 0) {
            return min($requestedLimit, $maxLimit);
        }

        return $maxLimit;
    }
}
