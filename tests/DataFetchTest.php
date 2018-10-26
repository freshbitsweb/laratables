<?php

namespace Freshbitsweb\Laratables\Tests;

class DataFetchTest extends TestCase
{

    /** @test */
    public function it_returns_the_data_as_expected()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-test', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                "0" => 1,
                "1" => $users->first()->name,
                "2" => $users->first()->email,
            ]],
        ]);
    }
}