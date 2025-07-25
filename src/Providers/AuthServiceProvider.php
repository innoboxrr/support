<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;

class AuthServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->mapPolicies();
    }

    public function mapPolicies()
    {
        // Define una clave única para el caché
        $cacheKey = 'support_auth_policies';

        // Intenta recuperar el mapeo de políticas desde el caché
        $policies = Cache::remember($cacheKey, now()->addDay(), function () {
            return $this->customDiscoverPolicies();
        });

        // Registra las políticas
        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Descubre las políticas de los modelos.
     *
     * @return array
     */
    protected function customDiscoverPolicies()
    {
        $policies = [];

        foreach (glob(__DIR__ . '/../Policies/*.php') as $file) {
            $policy = 'Innoboxrr\Support\Policies\\' . substr(basename($file), 0, -4);
            $model = 'Innoboxrr\Support\Models\\' . str_replace('Policy', '', $policy);

            if (class_exists($model) && class_exists($policy)) {
                $policies[$model] = $policy;
            }
        }

        return $policies;
    }

}
