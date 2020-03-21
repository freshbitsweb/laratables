<?php

namespace Freshbitsweb\Laratables\Tests\Traits;

trait PreparesDatatablesUrl
{
    protected function getDatatablesUrlParameters(string $searchValue = '', int $lengthValue = 10, array $order = [], array $extraColumns = []): array
    {
        $parameters = [
            'draw' => 1,
            'start' => 0,
            'length' => $lengthValue,
            'search' => [
                'value' => $searchValue,
            ],
        ];

        $parameters['columns'] = $this->getColumns($extraColumns);

        $parameters['order'] = $order ?: $this->getOrdering();

        return $parameters;
    }

    private function getColumns(array $extraColumns): array
    {
        $columns = collect(array_merge(['id', 'name', 'email', 'action', 'country.name', 'created_at'], $extraColumns));

        return $columns->map(function ($column, $index) use ($extraColumns) {
            $searchable = $orderable = true;

            $extraColumns = array_merge($extraColumns, ['action', 'country.name']);

            if (in_array($column, $extraColumns)) {
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
