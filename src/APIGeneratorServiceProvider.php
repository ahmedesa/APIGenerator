<?php

namespace essa\APIGenerator;

use essa\APIGenerator\Commands\GenerateComponent;
use Illuminate\Support\ServiceProvider;

class APIGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateComponent::class,
            ]);
        }
    }
}
