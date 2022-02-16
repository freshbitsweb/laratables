<?php

namespace Freshbitsweb\Laratables;

use Freshbitsweb\Laratables\Exceptions\IncorrectOrderColumn;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ColumnManager
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
     * @var string Primary column of the model
     */
    protected $primaryColumn;

    /**
     * @var \Illuminate\Support\Collection Requested columns
     */
    protected $requestedColumns;

    /**
     * @var array Columns to be selected
     */
    protected $selectColumns = [];

    /**
     * @var array Columns to search for
     */
    protected $searchColumns = [];

    /**
     * @var RelationshipsManager object
     */
    protected $relationshipsManager;

    /**
     * Initialize properties.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @param Class to customize query/data/logic
     * @return void
     */
    public function __construct($model, $class)
    {
        $this->initializeProperties($model, $class);
        $this->relationshipsManager = new RelationshipsManager($class, $this->modelObject);
        $this->setColumnProperties();
        $this->addAdditionalColumns();
        $this->addSearchableColumns();
    }

    /**
     * Initializes class properties.
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @param Class to customize query/data/logic
     * @return void
     */
    protected function initializeProperties($model, $class)
    {
        $this->class = $class;
        $this->modelObject = new $model();
        $this->primaryColumn = $this->modelObject->getKeyName();

        $requestData = collect(request()->all());
        $this->requestedColumns = collect($requestData->get('columns'));
    }

    /**
     * Set column properties from the request data.
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
     * Set column properties from the specified column data.
     *
     * @param array column details
     * @return void
     */
    protected function setColumnPropertiesFor($column)
    {
        $columnName = $column['name'];

        if ($column['searchable'] == 'true' && ! in_array($columnName, config('laratables.non_searchable_columns'))) {
            $this->searchColumns[] = $columnName;
        }

        if ($this->isCustomColumn($columnName) && ! isRelationColumn($columnName)) {
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
     * Decides wether specified column name is custom column. Returns method name if yes.
     *
     * @param string Name of the column
     * @return bool|string
     */
    public function isCustomColumn($columnName)
    {
        $columnName = str_replace('.', '_', $columnName);
        $methodName = Str::camel('laratables_custom_'.$columnName);

        if (method_exists($this->class, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Adds additional select columns to the array from the class, if any.
     *
     * @return void
     */
    protected function addAdditionalColumns()
    {
        if (method_exists($this->class, 'laratablesAdditionalColumns')) {
            array_push($this->selectColumns, ...$this->class::laratablesAdditionalColumns());
        }
    }

    /**
     * Adds additional search columns to the array from the class, if any.
     *
     * @return void
     */
    protected function addSearchableColumns()
    {
        if (method_exists($this->class, 'laratablesSearchableColumns')) {
            array_push($this->searchColumns, ...$this->class::laratablesSearchableColumns());
        }
    }

    /**
     * Returns the names of the columns with direction for ordering.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws IncorrectOrderColumn
     */
    public function getOrderColumns()
    {
        $requestedColumnNames = $this->getRequestedColumnNames()->toArray();

        $order = request('order');

        return collect($order)
            ->map(function ($item) use ($requestedColumnNames) {
                $orderColumn = $requestedColumnNames[$item['column']];

                if ($methodName = $this->hasCustomOrdering($orderColumn)) {
                    return [
                        $this->class::$methodName(),
                        $item['dir'],
                    ];
                }

                if ($methodName = $this->hasCustomRawOrdering($orderColumn)) {
                    return $this->class::$methodName($item['dir']);
                }

                if (in_array($orderColumn, $this->getSelectColumns()) === false) {
                    throw IncorrectOrderColumn::name($orderColumn);
                }

                return [
                    $orderColumn,
                    $item['dir'],
                ];
            })
        ;
    }

    /**
     * Decides weather there is a custom ordering for the specified column name. Returns method name if yes.
     *
     * @param string Name of the column
     * @return bool|string
     */
    public function hasCustomOrdering($orderColumn)
    {
        $methodName = Str::camel('laratables_order_'.$orderColumn);

        if (method_exists($this->class, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Decides weather there is a custom raw ordering for the specified column name. Returns method name if yes.
     *
     * @param string Name of the column
     * @return bool|string
     */
    public function hasCustomRawOrdering($orderColumn)
    {
        $methodName = Str::camel('laratables_order_raw_'.$orderColumn);

        if (method_exists($this->class, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Returns the list of searchable columns.
     *
     * @return array
     */
    public function getSearchColumns()
    {
        return $this->searchColumns;
    }

    /**
     * Returns the relations to be loaded by query.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relationshipsManager->getRelations();
    }

    /**
     * Returns the list of request columns.
     *
     * @return Collection
     */
    public function getRequestedColumnNames()
    {
        return $this->requestedColumns->pluck('name');
    }

    /**
     * Returns the list of select columns.
     *
     * @return array
     */
    public function getSelectColumns()
    {
        return $this->selectColumns;
    }
}
