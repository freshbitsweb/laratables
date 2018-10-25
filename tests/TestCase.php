<?php
namespace Freshbitsweb\Laratables\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Freshbitsweb\Laratables\LaratablesServiceProvider'];
    }
}