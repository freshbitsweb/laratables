<?php

namespace Freshbitsweb\Laratables\Tests;

class DatatableAttributesTest extends TestCase
{
    /** @test */
    public function it_returns_the_simple_data_as_expected()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-simple', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                'DT_RowId' => config('laratables.row_id_prefix').'1',
                'DT_RowClass' => 'text-success',
                'DT_RowData' => ['id' => 1],
            ]],
        ]);
    }
}
