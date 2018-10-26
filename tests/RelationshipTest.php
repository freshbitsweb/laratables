<?php

namespace Freshbitsweb\Laratables\Tests;

class RelationshipTest extends TestCase
{
    /** @test */
    public function it_returns_the_relationship_column_value()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-simple', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                "0" => 1,
                "1" => $users->first()->name,
                "2" => $users->first()->email,
                "3" => "<a>1</a>",
                "4" => $users->first()->country->name,
            ]],
        ]);
    }
}