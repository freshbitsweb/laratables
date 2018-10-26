<?php

namespace Freshbitsweb\Laratables\Tests;

use Freshbitsweb\Laratables\Tests\Traits\CreatesUsers;

class DataFetchTest extends TestCase
{
    use CreatesUsers;

    /** @test */
    public function check()
    {
        $this->createUsers();

        $this->assertDatabaseHas('users', [
            'id' => 1,
        ]);
    }
}