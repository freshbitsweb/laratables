<?php

namespace Freshbitsweb\Laratables\Tests\Traits;

trait PreparesDatatablesUrl
{
    /**
     * Prepares and returns the datatables fetch url.
     *
     * @param string Search value
     * @return array
     */
    protected function getDatatablesUrlParameters($searchValue = '')
    {
        $parameters = [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => [
                'value' => $searchValue,
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
        $columns = collect(['id', 'name', 'email', 'action', 'country.name', 'created_at']);

        return $columns->map(function ($column, $index) {
            $searchable = $orderable = true;

            if (in_array($column, ['action', 'country.name'])) {
                $searchable = $orderable = false;
            }

            return [
                'data' => $index,
                'name' => $column,
                'searchable' => $searchable,
                'orderable' => $orderable,
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
