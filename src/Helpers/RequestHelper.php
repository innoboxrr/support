<?php

namespace Innoboxrr\Support\Helpers;

use Illuminate\Http\Request;

class RequestHelper
{

    public static function getCookie($key)
    {
        // Obtén los cookies desde los encabezados
        $cookies = request()->headers->get('cookie');
        
        // Valida si los cookies están presentes
        if (!$cookies) {
            return null;
        }

        // Convierte los cookies en un array asociativo para un manejo más fácil
        $cookiesArray = collect(explode(';', $cookies))->mapWithKeys(function ($cookie) {
            $parts = explode('=', trim($cookie), 2); // Limita a 2 partes por posibles valores con '='
            return count($parts) === 2 ? [trim($parts[0]) => trim($parts[1])] : [];
        });

        // Retorna el valor de la cookie solicitada o null si no existe
        return $cookiesArray->get($key, null);
    }

    /**
     * Ejecuta un FormRequest simulando un request y usuario autenticado.
     * Útil para Jobs, seeds, procesos en background, etc.
     *
     * @param string $formRequestClass
     * @param array $data
     * @param \App\Models\User $user
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException|\Illuminate\Auth\Access\AuthorizationException
     */
    public static function handleFormRequestWithUser($formRequestClass, $method, $data, $user)
    {
        $request = $formRequestClass::create('/fake-url', $method, $data);
        $request->setUserResolver(fn() => $user);
        $request->validateResolved();
        if(!method_exists($request, 'handle')) {
            throw new \RuntimeException("The request class {$formRequestClass} must implement a handle method.");
        }
        return $request->handle();
    }
}