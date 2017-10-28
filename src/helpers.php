<?php

if (! function_exists('isRelationColumn')) {
    /**
     * Decides whether specified column is a relation table column
     *
     * @param string Name of the column
     * @return boolean
     */
    function isRelationColumn($columnName)
    {
        return str_contains($columnName, '.');
    }
}

if (! function_exists('getRelationDetails')) {
    /**
     * Returns the relation details from the specified column
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
