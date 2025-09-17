<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGrupo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $grupo): Response
    {
        // Obtener los grupos del usuario desde sesión
        $gruposUsuario = session('grupos', []);

        // Convertir los grupos permitidos en array (coma-separados)
        $gruposPermitidosArray = array_map('trim', explode(',', $grupo));

        // Verificar si hay al menos un grupo en común
        $interseccion = array_intersect($gruposUsuario, $gruposPermitidosArray);

        dd($interseccion);
        if (empty($interseccion)) {
            abort(403, 'Acceso denegado.');
        }

        return $next($request);
    }
}
