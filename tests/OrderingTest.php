<?php

namespace Freshbitsweb\Laratables\Tests;

class OrderingTest extends TestCase
{
    /** @test */
    public function it_orders_ther_records_as_expected()
    {
        $user1 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'Z',
            ]
        )->first();

        $user2 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'A',
            ]
        )->first();

        $response = $this->json(
            'GET',
            '/datatables-custom-order',
            $this->getDatatablesUrlParameters()
        );

        $response->assertJson([
            'recordsTotal' => 2,
            'data' => [
                [
                    '0' => 2,
                    '1' => $user2->name,
                ],
                [
                    '0' => 1,
                    '1' => $user1->name,
                ],
            ],
        ]);
    }
}
