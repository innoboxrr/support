<?php

namespace Innoboxrr\Support\Http\Requests;

class RequestFormater
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public static function format($request)
    {
        $instance = new static($request);
        $instance->prepareForValidation();
    }

    protected function prepareForValidation()
    {
        $flattened = $this->flattenArray($this->request->all());
        $this->request->replace($flattened);
    }
    
    protected function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
    
        foreach ($array as $key => $value) {
            $composedKey = $prefix ? "{$prefix}_{$key}" : $key;
    
            if (is_array($value) && $this->isAssoc($value) && ! $this->isPreservedArray($value)) {
                // Si es un array asociativo, lo aplanamos
                $result += $this->flattenArray($value, $composedKey);
            } else {
                // Si es un array plano o un valor simple, lo dejamos tal cual
                $result[$composedKey] = $value;
            }
        }
    
        return $result;
    }
    
    protected function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    protected function isPreservedArray(array $array): bool
    {
        // Preservamos si todos los elementos son arrays con claves tipo 'value' (como en emails o phones)
        return isset($array[0]) && is_array($array[0]) && array_key_exists('value', $array[0]);
    }
    
}