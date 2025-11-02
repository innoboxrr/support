<?php

namespace Innoboxrr\Support\Helpers;

class HttpHelper
{
    public static function getSubdomain($host, $mainDomain = null)
    {
        $mainDomain = $mainDomain ?? config('app.app_host');
        $pattern = '/^(?:(?<subdomain>.+)\.)?' . preg_quote($mainDomain, '/') . '$/';
        if (preg_match($pattern, $host, $matches)) {
            return $matches['subdomain'] ?? null;
        }
        return null;
    }
}
