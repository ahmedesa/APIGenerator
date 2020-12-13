<?php

namespace essa\APIGenerator;

use Illuminate\Support\ServiceProvider;
use essa\APIGenerator\Commands\GenerateComponent;

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
        if ($this->app->runningInConsole() && function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../config/jsonapi.php' => config_path('jsonapi.php'),
            ], 'config');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateComponent::class,
            ]);
        }
    }
}
