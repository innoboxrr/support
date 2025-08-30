<?php

namespace Innoboxrr\Support\Utils;

use Illuminate\Support\Arr;

class DataContainer
{
    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Retorna solo los campos especificados en el array.
     *
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        return Arr::only($this->data, $keys);
    }

    /**
     * Excluye los campos especificados en el array.
     *
     * @param array $keys
     * @return array
     */
    public function except(array $keys): array
    {
        return Arr::except($this->data, $keys);
    }

    /**
     * Retorna todos los datos.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Verifica si existe un campo usando dot notation.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Obtiene el valor de un campo usando dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Asigna un valor a una propiedad usando dot notation.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value, $override = true): void
    {
        if (!$override && Arr::has($this->data, $key)) {
            return;
        }
        Arr::set($this->data, $key, $value);
    }

    /**
     * Elimina una clave específica del contenedor de datos usando dot notation.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        Arr::forget($this->data, $key);
    }

    /**
     * Método mágico para acceder a los valores del array como propiedades.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Método mágico para asignar valores a las propiedades.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Método mágico para verificar si una propiedad existe en el array.
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * Realiza un merge de los datos existentes con nuevos datos.
     *
     * @param array $newData
     * @return void
     */
    public function merge(array $newData): void
    {
        $this->data = array_merge($this->data, $newData);
    }

    /**
     * Realiza un merge recursivo de los datos existentes con nuevos datos.
     *
     * @param array $newData
     * @return void
     */
    public function mergeRecursive(array $newData): void
    {
        $this->data = array_merge_recursive($this->data, $newData);
    }

    /**
     * Reemplaza los valores de los datos con nuevos valores.
     *
     * @param array $newData
     * @return void
     */
    public function replace(array $newData): void
    {
        $this->data = array_replace($this->data, $newData);
    }

    /**
     * Limpia todos los datos.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Cuenta el número de elementos en el contenedor de datos.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Devuelve las claves del contenedor de datos.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * Devuelve los valores del contenedor de datos.
     *
     * @return array
     */
    public function values(): array
    {
        return array_values($this->data);
    }

    /**
     * Filtra los datos usando un callback.
     *
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback): array
    {
        return array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Aplica una transformación a los datos.
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->data);
    }

    /**
     * Reduce los datos a un único valor.
     *
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * Divide los datos en grupos de tamaño especificado.
     *
     * @param int $size
     * @return array
     */
    public function chunk(int $size): array
    {
        return array_chunk($this->data, $size, true);
    }

    /**
     * Obtiene el primer elemento de los datos.
     *
     * @return mixed|null
     */
    public function first()
    {
        return reset($this->data) ?: null;
    }

    /**
     * Obtiene el último elemento de los datos.
     *
     * @return mixed|null
     */
    public function last()
    {
        return end($this->data) ?: null;
    }

    /**
     * Ordena los datos usando un callback o de forma predeterminada.
     *
     * @param callable|null $callback
     * @return void
     */
    public function sort(?callable $callback = null): void
    {
        if ($callback) {
            uasort($this->data, $callback);
        } else {
            asort($this->data);
        }
    }

    /**
     * Convierte los datos a formato JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }

    /**
     * Carga datos desde un string JSON.
     *
     * @param string $json
     * @return void
     */
    public function fromJson(string $json): void
    {
        $this->data = json_decode($json, true) ?? [];
    }

    /**
     * Devuelve los datos como un array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Verifica si el contenedor está vacío.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Verifica si todas las claves especificadas existen usando dot notation.
     *
     * @param array $keys
     * @return bool
     */
    public function keysExist(array $keys): bool
    {
        foreach ($keys as $key) {
            if (!Arr::has($this->data, $key)) {
                return false;
            }
        }
        return true;
    }

}