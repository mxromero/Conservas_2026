<?php

namespace App\Helpers;

class ZplHelper
{
    public static function generarDesdePlantilla(string $plantilla, array $valores): string
    {
        $carpeta = env('FOLDER_LABEL');
        $ruta = resource_path($carpeta . "/{$plantilla}.zpl");

        if (!file_exists($ruta)) {
            throw new \Exception("Plantilla ZPL no encontrada: {$plantilla}");
        }

        $contenido = file_get_contents($ruta);

        foreach ($valores as $clave => $valor) {
            $contenido = str_replace("[$clave]", $valor, $contenido);
        }

        return $contenido;
    }
}
