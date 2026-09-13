<?php

namespace Innoboxrr\Support\Http\Requests;

/**
 * Aplana los datos anidados de un formulario uniendo las claves con guion bajo.
 *
 *     ['seo' => ['title' => 'x', 'og' => ['image' => 'y']]]
 *     => ['seo_title' => 'x', 'seo_og_image' => 'y']
 *
 * Es lo que permite mandar grupos de campos de varios niveles y guardarlos
 * como metas planas (`seo_title`, `seo_og_image`), que es como las lee la
 * lista `editable_metas` de un modelo.
 *
 * Se conservan tal cual:
 *
 * - las listas (`['a', 'b']`), que se guardan enteras como JSON;
 * - las listas de objetos con `value`, como correos o teléfonos;
 * - un grupo vacío (`'seo' => []`), para que un formulario pueda vaciarlo.
 *
 * Si dos claves acaban siendo la misma (`seo_title` y `seo.title`), gana la
 * primera que aparece.
 */
class RequestFormater
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    /**
     * Sustituye el contenido de la petición por su versión aplanada. Las reglas
     * de validación que corran después tienen que usar los nombres aplanados.
     */
    public static function format($request)
    {
        $instance = new static($request);
        $instance->prepareForValidation();
    }

    /**
     * La misma transformación sobre un arreglo, sin tocar ninguna petición.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function flatten(array $data): array
    {
        return (new static())->flattenArray($data);
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

            // Un arreglo vacío no se aplana: antes contaba como asociativo y
            // desaparecía, así que vaciar un grupo en el formulario no borraba
            // nada.
            if (is_array($value) && $value !== [] && $this->isAssoc($value) && ! $this->isPreservedArray($value)) {
                $result += $this->flattenArray($value, $composedKey);
            } else {
                $result += [$composedKey => $value];
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
