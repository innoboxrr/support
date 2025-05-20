<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    public function map()
    {

        $this->mapApiRoutes();      

    }

    protected function mapApiRoutes()
    {

        foreach (glob(__DIR__ . '/../../routes/api/models/*.php') as $file) {

            $name = basename($file, '.php');

            Route::middleware('api')
                ->prefix('api/innoboxrr/support/' . $name)
                ->as('api.innoboxrr.support.' . $name . '.')
                ->namespace('Innoboxrr\Support\Http\Controllers')
                ->group($file);

        }

    }

}
