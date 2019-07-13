<?php

namespace Freshbitsweb\Laratables\Tests;

use Illuminate\Support\Str;

class RecordsLimitTest extends TestCase
{
    /** @test */
    public function it_can_limit_records_to_maximum_as_per_the_configuration()
    {
        $users = $this->createUsers(2);

        $response = $this->json(
            'GET',
            '/datatables-limit-1-record',
            $this->getDatatablesUrlParameters()
        );

        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_allow_unlimited_records_as_per_the_configuration()
    {
        $users = $this->createUsers(50);

        $response = $this->json(
            'GET',
            '/datatables-no-limit',
            $this->getDatatablesUrlParameters($searchValue = '', $lengthValue = -1)
        );

        $response->assertJsonCount(50, 'data');
    }

    /** @test */
    public function it_can_limit_records_based_on_the_request_even_when_unlimited_records_are_allowed()
    {
        $users = $this->createUsers(50);

        $response = $this->json(
            'GET',
            '/datatables-no-limit',
            $this->getDatatablesUrlParameters($searchValue = '', $lengthValue = 20)
        );

        $response->assertJsonCount(20, 'data');
    }

    /** @test */
    public function it_prioritizes_request_limit_compared_to_configuration_limit()
    {
        $users = $this->createUsers(50);

        $response = $this->json(
            'GET',
            '/datatables-max-limit-20',
            $this->getDatatablesUrlParameters()
        );

        $response->assertJsonCount(10, 'data');
    }
}
