<?php

namespace Freshbitsweb\Laratables;

class RelationshipsManager
{
    protected $relations = [];

    /**
     * Adds the relation to be loaded with the query
     *
     * @param string Name of the column
     * @return void
     */
    protected function addRelation($columnName)
    {
        list($relationName, $relationColumnName) = getRelationDetails($columnName);

        $this->relations[$relationName] = $this->getRelationQuery($relationColumnName);
    }

    /**
     * Returns a closure for fetching relation table data
     *
     * @param string Name of the relation table column
     * @return \Closure
     */
    protected function getRelationQuery($relationColumnName)
    {
        return function($query) use ($relationColumnName) {
            $query->select($query->getOwnerKey(), $relationColumnName);
        };
    }

    /**
     * Returns the (foreign key) column(s) to be selected for the relation table
     *
     * @param string Name of the column
     * @param \Illuminate\Database\Eloquent\Model The object of the model
     * @return array
     */
    protected function getRelationSelectColumns($columnName, $modelObject)
    {
        $relationName = getRelationName($columnName);

        // https://stackoverflow.com/a/25472778/3113599
        $relationType = (new \ReflectionClass($modelObject->$relationName()))->getShortName();
        $selectColumns = [];

        switch ($relationType) {
            case 'BelongsTo':
                $selectColumns[] = $modelObject->$relationName()->getForeignKey();
                break;
            case 'MorphTo':
                $selectColumns[] = $modelObject->$relationName()->getForeignKey();
                $selectColumns[] = $modelObject->$relationName()->getMorphType();
                break;
        }

        return $selectColumns;
    }

    /**
     * Returns the relations to be loaded by query
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
