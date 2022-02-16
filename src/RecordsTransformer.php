<?php

namespace Freshbitsweb\Laratables;

use Illuminate\Support\Str;

class RecordsTransformer
{
    /**
     * @var string Class with laratables methods
     */
    protected $class;

    /**
     * @var ColumnManager object
     */
    protected $columnManager;

    /**
     * Initialize properties.
     *
     * @param Class to customize query/data/logic
     * @return void
     */
    public function __construct($class, $columnManager)
    {
        $this->class = $class;
        $this->columnManager = $columnManager;
    }

    /**
     * Transforms each record for Datatables display.
     *
     * @param \Illuminate\Support\Collection Records of the table
     * @return \Illuminate\Support\Collection Records of the table
     */
    public function transformRecords($records)
    {
        if (method_exists($this->class, 'laratablesModifyCollection')) {
            $records = $this->class::laratablesModifyCollection($records)->values();
        }

        return $records->map(function ($item) {
            return $this->transformRecord($item);
        });
    }

    /**
     * Transform the record data for Datatables display.
     *
     * @param \Illuminate\Database\Eloquent\Model Eloquent object
     * @return \Illuminate\Database\Eloquent\Model Eloquent object
     */
    protected function transformRecord($record)
    {
        $columnNames = $this->columnManager->getRequestedColumnNames();
        $columnNames->transform(function ($item) use ($record) {
            return $this->getColumnValue($item, $record);
        });

        $dataTableParameters = $this->getDataTableParameters($record);

        return array_merge($dataTableParameters, $columnNames->toArray());
    }

    /**
     * Returns column value to be displayed in datatables.
     *
     * @param mixed Column value from database
     * @param \Illuminate\Database\Eloquent\Model Eloquent object
     * @return string
     */
    protected function getColumnValue($columnName, $record)
    {
        // Set custom column value from the class static method
        if ($methodName = $this->columnManager->isCustomColumn($columnName)) {
            return $this->class::$methodName($record);
        }

        if ($methodName = $this->customizesColumnValue($columnName)) {
            return $this->class::$methodName($record);
        }

        if (isRelationColumn($columnName)) {
            return $this->getRelationColumnValue($columnName, $record);
        }

        if ($this->isCarbonInstance($record->$columnName)) {
            return $record->$columnName->format(config('laratables.date_format', 'Y-m-d H:i:s'));
        }

        return $record->$columnName;
    }

    /**
     * Decides whether there is a custom method on the class for the specified column. Returns method name if yes.
     *
     * @param string Name of the column
     * @return bool|string
     */
    protected function customizesColumnValue($columnName)
    {
        $methodName = Str::camel('laratables_'.$columnName);

        if (method_exists($this->class, $methodName)) {
            return $methodName;
        }

        return false;
    }

    /**
     * Returns the value of relation table column.
     *
     * @param string Name of the column
     * @param \Illuminate\Database\Eloquent\Model Eloquent object
     * @return string
     */
    protected function getRelationColumnValue($columnName, $record)
    {
        [$relationName, $relationColumnName] = getRelationDetails($columnName);

        if ($methodName = $this->customizesColumnValue($relationName.'_'.$relationColumnName)) {
            return $this->class::$methodName($record);
        }

        if ($record->$relationName) {
            return $record->$relationName->$relationColumnName;
        }

        return 'N/A';
    }

    /**
     * Decides whether provided column value is a carbon date instance.
     *
     * @param mixed Column value
     * @return bool
     */
    protected function isCarbonInstance($columnValue)
    {
        return is_object($columnValue) &&
            (
                $columnValue instanceof \Carbon\Carbon ||
                $columnValue instanceof \Illuminate\Support\Carbon
            )
        ;
    }

    /**
     * Returns the dataTables specific parameters for the record.
     *
     * @param \Illuminate\Database\Eloquent\Model Eloquent object
     * @return array
     */
    public function getDataTableParameters($record)
    {
        $dataTableParameters = [
            'DT_RowId' => config('laratables.row_id_prefix').$record->{$record->getKeyName()},
        ];

        if (method_exists($this->class, 'laratablesRowClass')) {
            $dataTableParameters['DT_RowClass'] = $this->class::laratablesRowClass($record);
        }

        if (method_exists($this->class, 'laratablesRowData')) {
            $dataTableParameters['DT_RowData'] = $this->class::laratablesRowData($record);
        }

        return $dataTableParameters;
    }
}
