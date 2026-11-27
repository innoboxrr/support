# Changelog

Todas las modificaciones notables a este proyecto serán documentadas en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto sigue [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.2] - 2026-11-27

### Cambiado

- **Se desarrolla con `larapack-generator` 9.** La dependencia de desarrollo
  pedía `^7.0` y pasa a `^9.0`, la línea base del ecosistema. Para quien
  instala el paquete no cambia nada: `require-dev` no viaja a la aplicación.
  Cambia lo que se instala para desarrollarlo y probarlo: `./builder` genera con
  las plantillas de la 9 —claves ajenas en `restrict`, `managed` forzado en el
  IndexRequest, `$appendable` en el Resource— y la CI audita contra la línea
  base de hoy, no contra la de la 7.6.0, la única que se resolvía aquí (ver
  abajo).

  El paquete no usa ninguna clase del generador, sólo sus comandos, y el
  `composer.json` del generador es el mismo de la 7.10.3 a la 9.1.0: el resto
  de lo que se resuelve no cambia. Se fija `^9.0` en vez de ampliar a
  `^7.0 || ^8.0 || ^9.0` porque en desarrollo no hay consumidor al que dejarle
  la 7, y el rango ancho sólo serviría para que la CI resolviera un generador
  viejo.

- **La CI instala de verdad el generador.** `larapack-generator` depende de
  `innoboxrr/support ^2.1` desde la 7.7.0, y aquí `innoboxrr/support` es el
  paquete raíz: Composer adivinaba su versión de git (`dev-master`, o
  `dev-<sha>` en un pull request), no cumplía `^2.1` y descartaba todo
  generador posterior a la 7.6.0. La CI auditaba con la 7.6.0 sin decirlo, y
  con `^9.0` `composer update` no encontraría ninguno. `tests.yml` deja de
  llamar al reutilizable —un workflow llamado no recibe variables de entorno—
  y repite sus pasos con `COMPOSER_ROOT_VERSION` tomada de `VERSION`. Si el
  generador no queda instalado, el audit falla en vez de saltarse.

  Consecuencia para publicar: una 3.0.0 de este paquete no pasará la CI hasta
  que el generador admita `innoboxrr/support ^3.0`.

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
