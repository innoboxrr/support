# Changelog

Todas las modificaciones notables a este proyecto serán documentadas en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto sigue [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.1] - 2026-09-13

Lo que el paquete le hacía a la aplicación nueva de Laravel 13 que lo instala
al arrancar.

### Corregido

- `php artisan migrate` fallaba en una aplicación nueva con
  `CACHE_STORE=database`: `AuthServiceProvider` y `EventServiceProvider` leían
  la caché al arrancar, antes de que existiera la tabla `cache`. Ya no la usan,
  y las claves `support_auth_policies` y `support_events_and_observers` dejan
  de escribirse; las que queden en una caché existente se pueden borrar.
- La aplicación cargaba sus `routes/web.php` y `api.php` una vez más por este
  paquete: `RouteServiceProvider` heredaba del de Foundation, que vuelve a
  ejecutar el cargador de `withRouting()`.
- Cada usuario nuevo recibía repetido el correo de verificación:
  `EventServiceProvider` heredaba del de Foundation, que agrega otro
  `SendEmailVerificationNotification` para `Registered`.
- Sin las carpetas `Http/Events`, `Models` y `Observers`, el descubrimiento
  recorría la raíz del disco en cada arranque.

### Cambiado

- `RouteServiceProvider`, `EventServiceProvider` y `AuthServiceProvider` heredan
  de `Illuminate\Support\ServiceProvider`. Las rutas se registran en `boot()`,
  salvo con las rutas cacheadas; prefijo, nombres y middleware no cambian.
