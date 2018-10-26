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

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->withFactories(__DIR__.'/database/factories');
    }

    /**
     * Specify the package service provider.
     */
    protected function getPackageProviders($app)
    {
        return ['Freshbitsweb\Laratables\LaratablesServiceProvider'];
    }
}