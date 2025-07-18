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

}