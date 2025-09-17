<?php

namespace App\Helpers;

class VaciadoSession
{
    protected static string $key = 'vaciado_datos';

    /**
     * Obtener todos los datos del vaciado.
     */
    public static function all(): array
    {
        return session(self::$key, []);
    }

    /**
     * Obtener un dato específico.
     */
    public static function get(string $campo, $default = null)
    {
        return session(self::$key . '.' . $campo, $default);
    }

    /**
     * Establecer un campo o un array de campos.
     */
    public static function set(string|array $campo, $valor = null): void
    {
        $data = self::all();

        if (is_array($campo)) {
            $data = array_merge($data, $campo);
        } else {
            $data[$campo] = $valor;
        }

        session()->put(self::$key, $data);
    }

    /**
     * Eliminar un campo o todos.
     */
    public static function forget(string $campo = null): void
    {
        if ($campo) {
            $data = self::all();
            unset($data[$campo]);
            session()->put(self::$key, $data);
        } else {
            session()->forget(self::$key);
        }
    }
}
