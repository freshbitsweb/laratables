<?php

namespace Freshbitsweb\Laratables\Tests;

class DataFetchTest extends TestCase
{
    /** @test */
    public function it_returns_the_simple_data_as_expected()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-simple', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                "0" => 1,
                "1" => $users->first()->name,
                "2" => $users->first()->email,
            ]],
        ]);
    }

    /** @test */
    public function it_applies_the_clousure_condition_with_records_of_call()
    {
        $users = $this->createUsers(2);

        $response = $this->json('GET', '/datatables-records-of-closure', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                "0" => 1,
                "1" => $users->first()->name,
                "2" => $users->first()->email,
            ]],
        ]);
    }

    /** @test */
    public function it_applies_the_custom_query_conditions()
    {
        $users = $this->createUsers(2);

        $response = $this->json('GET', '/datatables-custom-query', $this->getDatatablesUrlParameters());

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