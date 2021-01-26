<?php

namespace ProBonoLabs\LaravelApiGenerator;

use Illuminate\Support\ServiceProvider;
use ProBonoLabs\LaravelApiGenerator\Console\Commands\CreateApi;

class RestApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/laravel-api-generator.php', 'laravel-api-generator');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/laravel-api-generator.php' => config_path('laravel-api-generator.php'),
        ]);
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateApi::class
            ]);
        }
    }
}
