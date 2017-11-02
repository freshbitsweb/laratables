<?php

namespace Freshbitsweb\Laratables;

class ColumnManager
{
    protected $model;

    protected $modelObject;

    protected $primaryColumn;

    protected $requestedColumns;

    protected $selectColumns;

    protected $searchColumns;

    protected $relationshipsManager;

    /**
     * Initialize properties
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    public function __construct($model)
    {
        $this->relationshipsManager = new RelationshipsManager();
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
            $this->relationshipsManager->addRelation($columnName);
            array_push($this->selectColumns, ...$this->relationshipsManager->getRelationSelectColumns($columnName));

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
    public function isCustomColumn($columnName)
    {
        $methodName = camel_case('datatables_custom_' . $columnName);

        if (method_exists($this->model, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Returns the list of searchable columns
     *
     * @return array
     */
    public function getSearchColumns()
    {
        return $this->searchColumns;
    }

    /**
     * Returns the relations to be loaded by query
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relationshipsManager->getRelations();
    }

    /**
     * Returns the list of request columns
     *
     * @return array
     */
    public function getRequestedColumnNames()
    {
        return $this->requestedColumns->pluck('name');
    }

    /**
     * Returns the list of select columns
     *
     * @return array
     */
    public function getSelectColumns()
    {
        return $this->selectColumns;
    }
}
