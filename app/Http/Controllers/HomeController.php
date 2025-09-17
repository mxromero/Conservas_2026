<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\User;
use App\Models\ModelsPaletizadoras;
use App\Models\ModelsProduccion;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $lineas = ModelsPaletizadoras::where('paletizadora', '!=', '0')
                                                ->orderBy('paletizadora', 'asc')
                                                ->get();

        // 1️⃣ Obtener todos los registros relevantes de producción de una sola vez
        $producciones = ModelsProduccion::whereIn('paletizadora', $lineas->pluck('paletizadora'))
            ->get(['paletizadora', 'material', 'NordPrev']);

        // 2️⃣ Contar cuántos registros hay para cada línea
        foreach ($lineas as $linea) {
            $material = trim($linea->material);

            $maxUma = ModelsProduccion::where('NordPrev', '0000091298')
                    ->where('paletizadora', '6')
                    ->where('material', 'SCO10454')
                    ->max('uma');
            $linea->ult_uma = $maxUma;
        }

        return view('home', compact('lineas'));
    }


    public function datos($paletizadora)
    {
        $linea = ModelsPaletizadoras::findOrFail($paletizadora);

        // Obtener registros de producción para esta línea
        $producciones = ModelsProduccion::where('paletizadora', $linea->paletizadora)
            ->where('exp_sap', '')
            ->get(['paletizadora', 'material', 'NordPrev']);

        // Contar cuántos registros hay
        $linea->exp_sap = $producciones->where('material', $linea->material)
            ->where('NordPrev', $linea->NordPrev)
            ->count();

        dd($linea);

        return response()->json($linea);
    }

    public function actualizarLineas()
    {
        $lineas = ModelsPaletizadoras::where('paletizadora', '!=', '0')
            ->orderBy('paletizadora', 'asc')
            ->get();

        $producciones = ModelsProduccion::whereIn('paletizadora', $lineas->pluck('paletizadora'))
            ->where('exp_sap', '')
            ->get(['paletizadora', 'material', 'NordPrev']);

        foreach ($lineas as $linea) {
            $maxUma = ModelsProduccion::where('NordPrev', '0000091298')
                    ->where('paletizadora', '6')
                    ->where('material', 'SCO10454')
                    ->max('uma');
            $linea->ult_uma = $maxUma;
        }

        return view('partials.lineas_cards', compact('lineas'));
    }


    function showRegistrationForm()
    {
        return view('auth.perfil');
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }


    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route('perfil')->with('success', 'Contraseña actualizada correctamente.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('perfil')->with('success', 'Usuario eliminado correctamente.');
    }

}
