<?php

namespace App\Http\Controllers;


use App\Models\ModelsImpresoras;
use App\Services;
use App\Services\SapService;
use App\Models\Paletizadoras;
use App\Models\ModelsPaletizadoras;
use App\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $paletizodoras = DB::table('PALETIZADORAS')
            ->leftJoin('descripcion','material','=','material_orden')
            ->select('paletizadoras.*',
                'descripcion.descripcion as descripcion'  // El campo LT x CJ de la tabla descripcion
            )
            ->where('paletizadora', '!=', '0')
            ->orderBy('paletizadora', 'asc')
            ->get();

        $totalLineas = DB::table('Lineas')->where('activa', '=', 'x')->count();

        return view('Configuracion.index', compact('paletizodoras','totalLineas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $paletizadora)
    {
        $configuracion = ModelsPaletizadoras::findOrFail($paletizadora);
        $data = $request->all();
        $data['centro'] = 'PDBU';
        $configuracion->update($data);
        return redirect()->back()->with('status', 'Configuración actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function disable($id){
        $paletizadora = ModelsPaletizadoras::findOrFail($id);
        $paletizadora->update(['eliminada' => 'X']);
        return redirect()->back()->with('status', 'Configuración desactivada exitosamente');
    }

    public function enable($id){
        $paletizadora = ModelsPaletizadoras::findOrFail($id);
        $paletizadora->update(['eliminada' => ' ']);
        return redirect()->back()->with('status', 'Configuración activada exitosamente');
    }

    public function consultaSap(Request $request){

        $valida_Orden = new SapService();
        $ordenPrevisional = str_pad($request->NordPrev, 10,'0',STR_PAD_LEFT);
        $parametros = ['WPlnum2' => $ordenPrevisional];////WPlnum2

        $response_array = $valida_Orden->obtenerDatos($parametros);

        return $response_array;

    }

    public function rol()
    {

        $roles = \Spatie\Permission\Models\Role::where('id','!=',1)->orderBy('id', 'asc')->get();

        return view('Configuracion.rol', compact('roles'));
    }

    public function createRol()
    {
        return view('Configuracion.create-rol');
    }

    public function storeRol(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255',
        ]);

        DB::table('roles')->insert([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('configuracion.rol')->with('success', 'Rol creado exitosamente');
    }

    public function editRol($id)
    {
        $rol = DB::table('roles')->where('id', $id)->first();

        if (!$rol) {
            return redirect()->route('configuracion.rol')->with('error', 'Rol no encontrado');
        }

        return view('Configuracion.edit-rol', compact('rol'));
    }

    public function updateRol(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
            'guard_name' => 'required|string|max:255',
        ]);

        DB::table('roles')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
                'updated_at' => now(),
            ]);

        return redirect()->route('configuracion.rol')->with('success', 'Rol actualizado exitosamente');
    }

    public function deleteRol($id)
    {
        // Aquí podrías verificar si el rol está siendo utilizado antes de eliminarlo

        DB::table('roles')->where('id', $id)->delete();

        return redirect()->route('configuracion.rol')->with('success', 'Rol eliminado exitosamente');
    }


    public function permisos()
    {
        $permisos = DB::table('permissions')->orderBy('id', 'asc')->get();
        return view('Configuracion.permisos', compact('permisos'));
    }

    public function createPermiso()
    {
        return view('Configuracion.create-permiso');
    }
    public function storePermiso(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|max:255',
        ]);

        DB::table('permissions')->insert([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('configuracion.permisos')->with('success', 'Permiso creado exitosamente');
    }
    public function editPermiso($id)
    {
        $permisos = DB::table('permissions')->where('id', $id)->first();

        if (!$permisos) {
            return redirect()->route('configuracion.permisos')->with('error', 'Permiso no encontrado');
        }

        return view('Configuracion.edit-permiso', compact('permisos'));
    }

    public function updatePermiso(Request $request, $id)
    {
        $request->merge(['guard_name' => 'web']);

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$id,
            'guard_name' => 'required|string|max:255',
        ]);

        DB::table('permissions')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
                'updated_at' => now(),
            ]);

        return redirect()->route('configuracion.permisos')->with('success', 'Permiso actualizado exitosamente');
    }
    public function deletePermiso($id)
    {
        // Aquí podrías verificar si el permiso está siendo utilizado antes de eliminarlo

        DB::table('permissions')->where('id', $id)->delete();

        return redirect()->route('configuracion.permisos')->with('success', 'Permiso eliminado exitosamente');
    }
    public function getPermisos()
    {
        $permisos = DB::table('permissions')->orderBy('id', 'asc')->get();
        return response()->json($permisos);
    }


    public function usuarios(){
        $usuarios = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name')
            ->orderBy('users.id', 'asc')
            ->get();

        return view('Configuracion.usuarios', compact('usuarios'));

    }

    public function createUsuario()
    {
        $roles = DB::table('roles')->where('id','!=',1)->orderBy('id', 'asc')->get();
        return view('Configuracion.create-usuario', compact('roles'));
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $usuarioId = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $request->role_id,
            'model_type' => 'App\Models\User',
            'model_id' => $usuarioId,
        ]);

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario creado exitosamente');
    }
    public function editUsuario($id)
    {
        $usuario = DB::table('users')->where('id', $id)->first();
        $roles = DB::table('roles')->where('id','!=',1)->orderBy('id', 'asc')->get();

        if (!$usuario) {
            return redirect()->route('configuracion.usuarios')->with('error', 'Usuario no encontrado');
        }

        return view('Configuracion.edit-usuario', compact('usuario', 'roles'));
    }
    public function updateUsuario(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $usuario = DB::table('users')->where('id', $id)->first();

        if (!$usuario) {
            return redirect()->route('configuracion.usuarios')->with('error', 'Usuario no encontrado');
        }

        DB::table('users')->where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $usuario->password,
            'updated_at' => now(),
        ]);

        DB::table('model_has_roles')->where('model_id', $id)->update([
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario actualizado exitosamente');
    }
    public function deleteUsuario($id)
    {
        DB::table('users')->where('id', $id)->delete();
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        return redirect()->route('configuracion.usuarios')->with('success', 'Usuario eliminado exitosamente');
    }



    public function lineas()
    {
        $lineas = DB::table('PALETIZADORAS')
                      ->where('paletizadora', '!=', '0')
                      ->orderBy('PALETIZADORA', 'desc')->get();
        $totalLineas = count($lineas) + 1;

        return view('Configuracion.lineas', compact('totalLineas','lineas'));
    }
    public function createLinea()
    {
        return view('Configuracion.create-linea');
    }
    public function storeLinea(Request $request)
    {
        $request->validate([
            'paletizadora' => 'required|string|max:255|unique:lineas,linea',
        ]);

        DB::table('PALETIZADORAS')->insert([
            'paletizadora' => $request->paletizadora,
        ]);

        return redirect()->route('configuracion.lineas')->with('success', 'Linea creada exitosamente');
    }
    public function editLinea($id)
    {
        $linea = DB::table('PALETIZADORAS')->where('id', $id)->first();

        if (!$linea) {
            return redirect()->route('configuracion.lineas')->with('error', 'Linea no encontrada');
        }

        return view('Configuracion.edit-linea', compact('linea'));
    }
    public function updateLinea(Request $request, $id)
    {
        $request->validate([
            'linea' => 'required|string|max:255|unique:lineas,linea,'.$id
        ]);

        DB::table('PALETIZADORAS')
            ->where('PALETIZADORA', $id)
            ->update([
                'linea' => $request->linea
            ]);

        return redirect()->route('configuracion.lineas')->with('success', 'Linea actualizada exitosamente');
    }
    public function deleteLinea($id)
    {

        DB::table('PALETIZADORAS')->where('PALETIZADORA', $id)->delete();

        return redirect()->route('configuracion.lineas')->with('success', 'Linea eliminada exitosamente');
    }
}

