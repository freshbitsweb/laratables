<?php

namespace Freshbitsweb\Laratables\Tests;

class OrderingTest extends TestCase
{
    /** @test */
    public function it_orders_the_records_as_expected()
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
            '/datatables-order',
            $this->getDatatablesUrlParameters()
        );

        $response->assertJson([
            'recordsTotal' => 2,
            'data' => [
                [
                    '0' => 1,
                    '1' => $user1->name,
                ],
                [
                    '0' => 2,
                    '1' => $user2->name,
                ],
            ],
        ]);
    }

    /** @test */
    public function it_orders_the_records_as_per_custom_ordering()
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

        $order = [
            [
                'column' => 6,
                'dir' => 'asc',
            ],
        ];

        $response = $this->json(
            'GET',
            '/datatables-custom-order',
            $this->getDatatablesUrlParameters(
                $searchValue = '',
                $lengthValue = 10,
                $order,
                $extraColumns = ['username']
            )
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

    /** @test */
    public function it_orders_the_records_with_order_by_raw()
    {
        $user1 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'Z',
                'email' => 'x@test.com',
            ]
        )->first();

        $user2 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'A',
                'email' => 'z@test.com',
            ]
        )->first();

        $user3 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'A',
                'email' => 'a@test.com',
            ]
        )->first();

        $response = $this->json(
            'GET',
            '/datatables-custom-order-raw',
            $this->getDatatablesUrlParameters()
        );

        $response->assertJson([
            'recordsTotal' => 3,
            'data' => [
                [
                    '0' => 1,
                    '1' => $user1->name,
                ],
                [
                    '0' => 3,
                    '1' => $user3->name,
                ],
                [
                    '0' => 2,
                    '1' => $user2->name,
                ],
            ],
        ]);
    }

    /** @test */
    public function it_orders_the_records_by_multi_column_order()
    {
        $user1 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'B',
                'email' => 'z@test.com',
            ]
        )->first();

        $user2 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'A',
                'email' => 'a@test.com',
            ]
        )->first();

        $user3 = $this->createUsers(
            $count = 1,
            $parameters = [
                'name' => 'A',
                'email' => 'b@test.com',
            ]
        )->first();

        $order = [
            [
                'column' => 1,
                'dir' => 'asc',
            ],
            [
                'column' => 2,
                'dir' => 'desc',
            ],
        ];

        $response = $this->json(
            'GET',
            '/datatables-multi-column-order',
            $this->getDatatablesUrlParameters($searchValue = '', $lengthValue = 10, $order)
        );

        $response->assertJson([
            'recordsTotal' => 3,
            'data' => [
                [
                    '0' => $user3->id,
                    '1' => $user3->name,
                ],
                [
                    '0' => $user2->id,
                    '1' => $user2->name,
                ],
                [
                    '0' => $user1->id,
                    '1' => $user1->name,
                ],
            ],
        ]);
    }
}
