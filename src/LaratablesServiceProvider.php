<?php

namespace Freshbitsweb\Laratables;

class LaratablesServiceProvider extends ServiceProvider
{
    /**
     * Publish configuration file
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laratables.php' => config_path('laratables.php')
        ], 'config');
    }
}
