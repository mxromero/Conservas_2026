<?php

namespace App\Http\Controllers;

use App\Models\ModelsCantidadMaterial;
use Illuminate\Http\Request;

class ControllerCantidadMaterial extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Todo_materiales = ModelsCantidadMaterial::orderBy('Material')->get();
        return view('configuracion.cantidad-material', compact('Todo_materiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('configuracion.cantidad-material-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Material' => 'required|string|max:10',
            'linea' => 'required|string|max:10',
            'cant_pro' => 'required|integer|min:1',
        ]);

        ModelsCantidadMaterial::create([
            'Material' => $request->Material,
            'linea' => $request->linea,
            'cant_pro' => $request->cant_pro,
            'corr_actual' => 0, // puedes ajustarlo
            'nvo_lote' => 'N',  // valor por defecto
        ]);

        return redirect()->route('configuracion.cantidad-material')
                        ->with('success', 'Material agregado correctamente.');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idMaterial, string $idPaletizadora)
    {
            $material = ModelsCantidadMaterial::where('Material', '=', $idMaterial)
                                              ->where('linea', '=', $idPaletizadora)
                                              ->firstOrFail();
            $material->delete();

            return redirect()->route('configuracion.cantidad-material')
                ->with('success', 'Material eliminado correctamente.');
    }
}
