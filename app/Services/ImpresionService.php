<?php

namespace App\Services;

class ImpresionService
{
    public function imprimir($zpl, $direccionIP, $puerto = 9100)
    {
        // Crear el socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new \Exception("Error al crear el socket: " . socket_strerror(socket_last_error()));
        }

        // Conectar a la impresora
        $resultado = socket_connect($socket, $direccionIP, $puerto);
        if ($resultado === false) {
            throw new \Exception("Error al conectar a la impresora: " . socket_strerror(socket_last_error()));
        }

        // Enviar el código ZPL
        socket_write($socket, $zpl, strlen($zpl));

        // Cerrar el socket (opcional, pero buena práctica)
        socket_close($socket);
    }
}
