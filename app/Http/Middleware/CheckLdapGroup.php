<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckLdapGroup
{
    public function handle($request, Closure $next, ...$gruposPermitidos)
    {
            $user = Auth::user();

            if (!$user) {
                return redirect('login');
            }
            // Obtener los grupos del usuario autenticado
            $userGroups = session('grupos');

            // 🔹 Si el usuario es Admin, puede acceder a cualquier ruta
            if (in_array('UP_Conservas_Admin', $userGroups)) {
                return $next($request);
            }

            // 🔹 Si no es admin, validar que pertenezca a los grupos permitidos
            foreach ($gruposPermitidos as $grupo) {
                if (in_array($grupo, $userGroups)) {
                    return $next($request);
                }
            }

            abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
