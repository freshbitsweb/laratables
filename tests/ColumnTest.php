<?php

namespace Freshbitsweb\Laratables\Tests;

use Illuminate\Support\Str;

class ColumnTest extends TestCase
{
    /** @test */
    public function it_returns_the_custom_column_value()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-simple', $this->getDatatablesUrlParameters());

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

    /** @test */
    public function it_returns_the_customized_column_value()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-customized-column', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                '0' => 1,
                '1' => Str::limit($users->first()->name, 5),
                '2' => $users->first()->email,
                '3' => '<a>1</a>',
            ]],
        ]);
    }

    /** @test */
    public function it_loads_additional_columns()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-additional-column', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                '0' => 1,
                '1' => $users->first()->remember_token,
                '2' => $users->first()->email,
                '3' => '<a>1</a>',
            ]],
        ]);
    }

    /** @test */
    public function it_converts_carbon_to_dates()
    {
        $users = $this->createUsers();

        $response = $this->json('GET', '/datatables-simple', $this->getDatatablesUrlParameters());

        $response->assertJson([
            'recordsTotal' => 1,
            'data' => [[
                '0' => 1,
                '1' => $users->first()->name,
                '2' => $users->first()->email,
                '3' => '<a>1</a>',
                '4' => $users->first()->country->name,
                '5' => $users->first()->created_at->format(config('laratables.date_format')),
            ]],
        ]);
    }
}
