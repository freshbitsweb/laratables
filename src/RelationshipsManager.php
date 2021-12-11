<?php

namespace Freshbitsweb\Laratables;

use Illuminate\Support\Str;

class RelationshipsManager
{
    /**
     * @var string Class with laratables methods
     */
    protected $class;

    /**
     * @var Eloquent Model object
     */
    protected $modelObject;

    /**
     * @var array Relations to be eager loaded
     */
    protected $relations = [];

    /**
     * Initialize properties.
     *
     * @param Class to customize query/data/logic
     * @param Eloquent Model object
     * @return void
     */
    public function __construct($class, $modelObject)
    {
        $this->class = $class;
        $this->modelObject = $modelObject;
    }

    /**
     * Adds the relation to be loaded with the query.
     *
     * @param string Name of the column
     * @return void
     */
    public function addRelation($columnName)
    {
        $relationName = getRelationName($columnName);

        if (
            ! array_key_exists($relationName, $this->relations) &&
            ! in_array($relationName, $this->relations)
        ) {
            $methodName = Str::camel('laratables_'.$relationName.'relation_query');
            if (method_exists($this->class, $methodName)) {
                $this->relations[$relationName] = $this->class::$methodName();

                return;
            }

            $this->relations[] = $relationName;
        }
    }

    /**
     * Returns the (foreign key) column(s) to be selected for the relation table.
     *
     * @param string Name of the column
     * @return array
     */
    public function getRelationSelectColumns($columnName)
    {
        $relationName = getRelationName($columnName);

        return $this->decideRelationColumns($relationName);
    }

    /**
     * Decides the columns to be used based on the relationship.
     *
     * @param string Name of the relation
     * @return array
     */
    protected function decideRelationColumns($relationName)
    {
        // https://stackoverflow.com/a/25472778/3113599
        $relationType = (new \ReflectionClass($this->modelObject->$relationName()))->getShortName();
        $selectColumns = [];

        // Laravel 5.8 renamed getForeignKey() to getForeignKeyName()
        $methodName = method_exists($this->modelObject->$relationName(), 'getForeignKeyName') ?
            'getForeignKeyName' :
            'getForeignKey'
        ;

        switch ($relationType) {
            case 'BelongsTo':
                $selectColumns[] = $this->modelObject->$relationName()->{$methodName}();
                break;
            case 'MorphTo':
                $selectColumns[] = $this->modelObject->$relationName()->{$methodName}();
                $selectColumns[] = $this->modelObject->$relationName()->getMorphType();
                break;
        }

        return $selectColumns;
    }

    /**
     * Returns the relations to be loaded by query.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
