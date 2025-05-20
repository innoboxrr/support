<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        
        $this->mergeConfigFrom(__DIR__ . '/../../config/innoboxrr-support.php', 'innoboxrr-support');

    }

    public function boot()
    {
        
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // $this->loadViewsFrom(__DIR__.'/../../resources/views', 'innoboxrrsupport');

        if ($this->app->runningInConsole()) {
            
            // $this->publishes([__DIR__.'/../../resources/views' => resource_path('views/vendor/innoboxrrsupport'),], 'views');

            $this->publishes([__DIR__.'/../../config/innoboxrr-support.php' => config_path('innoboxrr-support.php')], 'config');

        }

    }
    
}