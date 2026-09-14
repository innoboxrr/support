<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Hereda de ServiceProvider y no del AuthServiceProvider de Foundation, igual
 * que los proveedores de rutas y de eventos: del de Foundation no se usaba
 * nada y cada politica se registra aqui con Gate::policy().
 */
class AuthServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->mapPolicies();
    }

    public function mapPolicies(): void
    {
        // Sin cache a proposito: leerla al arrancar rompe `php artisan migrate`
        // con CACHE_STORE=database antes de que exista la tabla `cache`, y la
        // clave quedaba compartida con cualquier otro paquete.
        foreach ($this->customDiscoverPolicies() as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Policies/{Modelo}Policy.php => Models/{Modelo}.php.
     *
     * @return array<class-string, class-string>
     */
    protected function customDiscoverPolicies(): array
    {
        $policies = [];

        foreach (glob(__DIR__ . '/../Policies/*.php') ?: [] as $file) {
            $name = basename($file, '.php');
            $policy = 'Innoboxrr\Support\Policies\\' . $name;

            // Con el nombre corto de la policy: con el completo la clase del
            // modelo nunca existia.
            $model = 'Innoboxrr\Support\Models\\' . substr($name, 0, -strlen('Policy'));

            if (class_exists($model) && class_exists($policy)) {
                $policies[$model] = $policy;
            }
        }

        return $policies;
    }

}
