<?php

namespace Freshbitsweb\Laratables\Tests;

class SearchTest extends TestCase
{
    /** @test */
    public function it_applies_the_search_as_per_the_custom_method()
    {
        $randomString = str_random(20);

        $users = $this->createUsers(
            $count = 1,
            $parameters = [
                'remember_token' => $randomString,
            ]
        );

        $response = $this->json(
            'GET',
            '/datatables-custom-search',
            $this->getDatatablesUrlParameters($searchValue = $randomString)
        );

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                '0' => 1,
                '1' => $users->first()->name,
                '2' => $users->first()->email,
                '3' => '<a>1</a>',
                '4' => $users->first()->country->name,
            ]],
        ]);
    }

    /** @test */
    public function it_applies_the_search_to_searchable_columns()
    {
        $randomString = str_random(20);

        $users = $this->createUsers(
            $count = 1,
            $parameters = [
                'remember_token' => $randomString,
            ]
        );

        $response = $this->json(
            'GET',
            '/datatables-searchable-columns',
            $this->getDatatablesUrlParameters($searchValue = $randomString)
        );

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                '0' => 1,
                '1' => $users->first()->name,
                '2' => $users->first()->email,
                '3' => '<a>1</a>',
            ]],
        ]);
    }
}
