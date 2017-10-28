<?php

namespace Freshbitsweb\Laratables;

class Laratables
{
    protected $query;

    protected $columnManager;

    protected $filterAgent;

    /**
     * Declare objects
     *
     * @param \Illuminate\Database\Eloquent\Model The model to work on
     * @return void
     */
    public function __construct($model)
    {
        $this->query = new Query($model);
        $this->columnManager = new ColumnManager($model);
        $this->filterAgent = new FilterAgent($model);
    }

    /**
     * Accepts datatables ajax request and returns table data
     *
     * @return array Table data
     */
    public static function recordsOf($model)
    {
        $instance = new static($model);

        $this->applyFilters();

        return $instance->tableData();
    }

    /**
     * Applies conditions to the query if search is performed in datatables
     *
     * @return void
     */
    protected function applyFiltersTo()
    {
        $searchValue = request('search')['value'];

        if ($searchValue) {
            $this->query = $instance->applyFiltersTo($this->query, $this->columnManager->getSearchColumns(), $searchValue);
        }
    }

    /**
     * Prepares and returns data for the datatables
     *
     * @return array
     */
    protected function tableData()
    {
        return [
            'draw' => $this->model,
            'recordsTotal' => 10,
            'recordsFiltered' => 10,
            'data' => [
                'a' => 'b'
            ],
        ];
    }
}
