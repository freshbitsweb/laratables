<?php

namespace Freshbitsweb\Laratables\Tests\Traits;

trait PreparesDatatablesUrl
{
    /**
     * Prepares and returns the datatables fetch url.
     *
     * @return array
     */
    protected function getDatatablesUrlParameters()
    {
        $parameters = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => [
                'value' => '',
            ],
        ];

        $parameters['columns'] = $this->getColumns();

        $parameters['order'] = $this->getOrdering();

        return $parameters;
    }

    /**
     * Returns columns for the parameters.
     *
     * @return array
     */
    private function getColumns()
    {
        $columns = collect(['id', 'name', 'email']);

        return $columns->map(function($column, $index) {
            return [
                'data' => $index,
                'name' => $column,
                'searchable' => true,
                'orderable' => true,
            ];
        })->toArray();
    }

    /**
     * Returns order/sort details for the parameters.
     *
     * @return array
     */
    private function getOrdering()
    {
        return [[
            'column' => 0,
            'dir' => 'asc',
        ]];
    }
}