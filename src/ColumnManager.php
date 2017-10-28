<?php

namespace Freshbitsweb\Laratables;

class ColumnManager
{
    protected $model;

    protected $modelObject;

    protected $primaryColumn;

    protected $selectColumns;

    /**
     * Initialize properties
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    public function __construct($model)
    {
        $this->initializeProperties($model);
        $this->setColumnProperties();
    }

    /**
     * Initializes class properties
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    protected function initializeProperties($model)
    {
        $this->model = $model;
        $this->modelObject = new $model;
        $this->primaryColumn = $this->modelObject->getKeyName();

        $requestData = collect(request()->all());
        $this->requestedColumns = collect($requestData->get('columns'));
    }

    /**
     * Set column properties from the request data
     *
     * @return void
     */
    protected function setColumnProperties()
    {
        // First of all, add the id (or any other primary key), if not available
        if (! $this->requestedColumns->contains('name', $this->primaryColumn)) {
            $this->selectColumns[] = $this->primaryColumn;
        }

        $this->requestedColumns->each(function ($column) {
            $this->setColumnPropertiesFor($column);
        });
    }

    /**
     * Set column properties from the specified column data
     *
     * @param array column details
     * @return void
     */
    protected function setColumnPropertiesFor($column)
    {
        $columnName = $column['name'];

        if ($this->isCustomColumn($columnName)) {
            return;
        }

        if ($column['searchable']) {
            $this->searchColumns[] = $columnName;
        }

        if (isRelationColumn($columnName)) {
            $this->addRelation($columnName);
            $this->addRelationSelectColumns($columnName);

            return;
        }

        $this->selectColumns[] = $columnName;
    }

    /**
     * Decides wether specified column name is custom column. Returns method name if yes
     *
     * @param string Name of the column
     * @return boolean|string
     */
    protected function isCustomColumn($columnName)
    {
        $methodName = camel_case('datatables_custom_' . $columnName);

        if (method_exists($this->model, $methodName)) {
            return $methodName;
        }

        return false;
    }

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
            $query->select($query->getOtherKey(), $relationColumnName);
        };
    }

    /**
     * Appends the (foreign key) column(s) to be selected for the relation table
     *
     * @param string Name of the column
     * @return void
     */
    protected function addRelationSelectColumns($columnName)
    {
        $relationName = $this->getRelationName($columnName);

        // https://stackoverflow.com/a/25472778/3113599
        $relationType = (new \ReflectionClass($this->modelObject->$relationName()))->getShortName();

        switch ($relationType) {
            case 'BelongsTo':
                $this->selectColumns[] = $this->modelObject->$relationName()->getForeignKey();
                break;
            case 'MorphTo':
                $this->selectColumns[] = $this->modelObject->$relationName()->getForeignKey();
                $this->selectColumns[] = $this->modelObject->$relationName()->getMorphType();
                break;
        }
    }

    /**
     * Returns the name of the relation for the column specified
     *
     * @param string Name of the column
     * @return string
     */
    protected function getRelationName($columnName)
    {
        list($relationName, $relationColumnName) = getRelationDetails($columnName);

        return $relationName;
    }

    /**
     * Returns the list of searchable columns
     *
     * @return array
     */
    protected function getSearchColumns()
    {
        return $this->searchColumns;
    }
}
