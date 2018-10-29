<?php

namespace Freshbitsweb\Laratables;

use Illuminate\Support\ServiceProvider;

class LaratablesServiceProvider extends ServiceProvider
{
    /**
     * Publish configuration file.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/laratables.php' => config_path('laratables.php'),
            ], 'laratables_config');
        }
    }

    /**
     * Make config publishment optional by merging the config from the package.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laratables.php',
            'laratables'
        );
    }
}
