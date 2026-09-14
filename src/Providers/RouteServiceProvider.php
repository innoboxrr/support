<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Hereda de ServiceProvider y no del RouteServiceProvider de Foundation. Aquel
 * guarda en una propiedad estatica de la clase base el cargador de rutas que
 * bootstrap/app.php registra con withRouting(), y cada subclase lo vuelve a
 * ejecutar: la aplicacion cargaba sus routes/web.php y api.php una vez mas por
 * cada paquete.
 */
class RouteServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        // Cuando las rutas estan cacheadas no hay que volver a registrarlas.
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        // Hoy el paquete no trae rutas: sin la carpeta el glob no encuentra nada.
        foreach (glob(__DIR__ . '/../../routes/api/models/*.php') ?: [] as $file) {

            $name = basename($file, '.php');

            Route::middleware('api')
                ->prefix('api/innoboxrr/support/' . $name)
                ->as('api.innoboxrr.support.' . $name . '.')
                ->namespace('Innoboxrr\Support\Http\Controllers')
                ->group($file);

        }
    }

}
