<?php

namespace Innoboxrr\Support\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Hereda de ServiceProvider y no del EventServiceProvider de Foundation: aquel
 * registra al arrancar otro listener SendEmailVerificationNotification para
 * Registered, y con uno por paquete la aplicacion mandaba el correo de
 * verificacion repetido.
 *
 * Enlaza los eventos de Http/Events con sus listeners y cada modelo con su
 * observer de Observers.
 */
class EventServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->registerEventsAndObservers();
    }

    protected function registerEventsAndObservers(): void
    {
        // Sin cache a proposito: leerla al arrancar rompe `php artisan migrate`
        // con CACHE_STORE=database antes de que exista la tabla `cache`, y la
        // clave quedaba compartida con cualquier otro paquete.
        foreach ($this->customDiscoverEvents() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->customDiscoverObservers() as $model => $observer) {
            $model::observe($observer);
        }
    }

    /**
     * Recorre Http/Events/{Modelo}/Events/*.php y empareja cada evento con los
     * listeners de Http/Events/{Modelo}/Listeners/{Evento}/*.php.
     *
     * @return array<class-string, array<int, class-string>>
     */
    protected function customDiscoverEvents(): array
    {
        $events = [];
        $basePath = realpath(__DIR__ . '/../Http/Events');

        // Sin la carpeta, realpath() da false y el glob recorreria la raiz del
        // disco.
        if ($basePath === false) {
            return $events;
        }

        $namespace = 'Innoboxrr\Support\Http\Events\\';

        foreach (glob("{$basePath}/*", GLOB_ONLYDIR) ?: [] as $modelPath) {
            $model = basename($modelPath);

            foreach (glob("{$modelPath}/Events/*.php") ?: [] as $eventPath) {
                $eventName = pathinfo($eventPath, PATHINFO_FILENAME);
                $eventClass = "{$namespace}{$model}\\Events\\{$eventName}";

                foreach (glob("{$modelPath}/Listeners/{$eventName}/*.php") ?: [] as $listenerPath) {
                    $listenerName = pathinfo($listenerPath, PATHINFO_FILENAME);

                    $events[$eventClass][] = "{$namespace}{$model}\\Listeners\\{$eventName}\\{$listenerName}";
                }
            }
        }

        return $events;
    }

    /**
     * Empareja Models/{Modelo}.php con Observers/{Modelo}Observer.php.
     *
     * @return array<class-string, class-string>
     */
    protected function customDiscoverObservers(): array
    {
        $observers = [];
        $modelsPath = realpath(__DIR__ . '/../Models');
        $observersPath = realpath(__DIR__ . '/../Observers');

        // Igual que con los eventos: sin las carpetas no hay nada que enlazar,
        // y el glob sobre false recorreria la raiz del disco.
        if ($modelsPath === false || $observersPath === false) {
            return $observers;
        }

        foreach (glob("{$modelsPath}/*.php") ?: [] as $modelFilePath) {
            $modelName = pathinfo($modelFilePath, PATHINFO_FILENAME);
            $modelClass = 'Innoboxrr\Support\Models\\' . $modelName;
            $observerClass = 'Innoboxrr\Support\Observers\\' . $modelName . 'Observer';

            if (file_exists("{$observersPath}/{$modelName}Observer.php") && class_exists($modelClass) && class_exists($observerClass)) {
                $observers[$modelClass] = $observerClass;
            }
        }

        return $observers;
    }

}
