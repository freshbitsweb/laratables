<?php

namespace Freshbitsweb\Laratables;

use Freshbitsweb\Laratables\Exceptions\IncorrectOrderColumn;

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
        $this->initializeProperties($model);
        $this->relationshipsManager = new RelationshipsManager($model, $this->modelObject);
        $this->setColumnProperties();
        $this->addAdditionalColumns();
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

        if ($column['searchable'] == 'true') {
            $this->searchColumns[] = $columnName;
        }

        if ($this->isCustomColumn($columnName)) {
            return;
        }

        if (isRelationColumn($columnName)) {
            $this->relationshipsManager->addRelation($columnName);

            if ($foreignKeys = $this->relationshipsManager->getRelationSelectColumns($columnName)) {
                array_push($this->selectColumns, ...$foreignKeys);
            }

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
        $methodName = camel_case('laratables_custom_' . $columnName);

        if (method_exists($this->model, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Adds additional select and search columns to the array from the model, if any
     *
     * @return void
     */
    protected function addAdditionalColumns()
    {
        if (method_exists($this->model, 'laratablesAdditionalColumns')) {
            array_push($this->selectColumns, ...$this->model::laratablesAdditionalColumns());
            array_push($this->searchColumns, ...$this->model::laratablesAdditionalColumns());
        }
    }

    /**
     * Returns the values for order by clause of the query
     *
     * @throws IncorrectOrderColumn
     *
     * @return array
     */
    public function getOrderBy()
    {
        $orderColumn = $this->getOrderColumn();
        $selectedColumnNames = $this->getSelectColumns();

        if (! in_array($orderColumn, $selectedColumnNames)) {
            throw IncorrectOrderColumn::name($orderColumn);
        }

        $order = request('order');
        return [$orderColumn, $order[0]['dir']];
    }

    /**
     * Returns the name of the column for ordering
     *
     * @return string
     */
    public function getOrderColumn()
    {
        $requestedColumnNames = $this->getRequestedColumnNames()->toArray();

        $order = request('order');

        $orderColumn = $requestedColumnNames[$order[0]['column']];

        if ($methodName = $this->hasCustomOrdering($orderColumn)) {
            $orderColumn = $this->model::$methodName($orderColumn);
        }

        return $orderColumn;
    }

    /**
     * Decides weather there is a custom ordering for the specified column name. Returns method name if yes
     *
     * @param string Name of the column
     * @return boolean|string
     */
    public function hasCustomOrdering($orderColumn)
    {
        $methodName = camel_case('laratables_order_' . $orderColumn);

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
