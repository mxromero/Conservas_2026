<?php

namespace App\Helpers;

class PrinterHelper
{
    /**
     * Intenta imprimir por IP, si falla, intenta por impresora compartida.
     */
    public static function imprimir(string $zpl, string $ip = null, string $impresoraCompartida = null, int $puerto = 9100)
    {
        $impresionPorIp = false;

        // 1️⃣ Intentar por IP
        if (!empty($ip)) {
            try {
                $fp = @fsockopen($ip, $puerto, $errno, $errstr, 2);
                if ($fp) {
                    fwrite($fp, $zpl);
                    fclose($fp);
                    $impresionPorIp = true; // Éxito por IP
                }
            } catch (\Throwable $e) {
                // Si falla, sigue al plan B
            }
        }

        // 2️⃣ Plan B: impresora compartida SOLO si falló la IP
        if (!$impresionPorIp && !empty($impresoraCompartida)) {
            $ruta = "\\\\" . $impresoraCompartida;
            $tmpFile = storage_path('app/temp_print.zpl');
            file_put_contents($tmpFile, $zpl);

            // Este comando funciona en Windows
            exec("COPY /B \"{$tmpFile}\" \"{$ruta}\"");

            unlink($tmpFile);
            return true;
        }

        return $impresionPorIp;
    }
}
