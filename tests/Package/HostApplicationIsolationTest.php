<?php

namespace Innoboxrr\Support\Tests\Package;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as AppEventServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as AppRouteServiceProvider;
use Innoboxrr\Support\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Lo que el paquete no debe hacerle a la aplicacion que lo instala.
 *
 * Se arma como bootstrap/app.php en Laravel 13: withRouting() deja el cargador
 * de rutas de la aplicacion en la clase base de Foundation y registra ese
 * proveedor, y withEvents(), que Application::configure() llama siempre,
 * registra el EventServiceProvider base. Asi se cuenta lo que el paquete agrega
 * encima de lo que la aplicacion ya tiene.
 */
final class HostApplicationIsolationTest extends TestCase
{
    public static int $appRouteLoads = 0;

    protected function defineEnvironment($app): void
    {
        self::$appRouteLoads = 0;

        AppRouteServiceProvider::loadRoutesUsing(function () {
            self::$appRouteLoads++;
        });

        $app->booting(function ($app) {
            $app->register(AppRouteServiceProvider::class, force: true);
            $app->register(AppEventServiceProvider::class);
        });
    }

    protected function tearDown(): void
    {
        AppRouteServiceProvider::loadRoutesUsing(null);

        parent::tearDown();
    }

    #[Test]
    public function la_aplicacion_carga_sus_rutas_una_sola_vez(): void
    {
        $this->assertSame(1, self::$appRouteLoads);
    }
}
