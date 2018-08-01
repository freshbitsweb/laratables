<?php

if (! function_exists('isRelationColumn')) {
    /**
     * Decides whether specified column is a relation table column.
     *
     * @param string Name of the column
     *
     * @return bool
     */
    function isRelationColumn($columnName)
    {
        return str_contains($columnName, '.');
    }
}

if (! function_exists('getRelationDetails')) {
    /**
     * Returns the relation details from the specified column.
     *
     * @param string Name of the column
     *
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
     *
     * @return string
     */
    function getRelationName($columnName)
    {
        list($relationName, $relationColumnName) = getRelationDetails($columnName);

        return $relationName;
    }
}
