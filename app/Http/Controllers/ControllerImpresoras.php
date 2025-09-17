<?php

namespace App\Http\Controllers;

use App\Models\ModelsImpresoras;
use Illuminate\Http\Request;

class ControllerImpresoras extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lineas = ModelsImpresoras::orderBy('orden')->get();


        return view('impresoras.index', compact('lineas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('impresoras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'linea' => 'required|string|unique:Lineas,orden',
            'impresora' => 'required|string',
            'impresorac' => 'required|string',
            'activa' => 'required|in:X,N',
        ], [
            'linea.unique' => 'Ya existe una paletizadora con ese número.',
        ]);

        ModelsImpresoras::create([
            'orden' => $request->linea,
            'linea' => $request->linea,
            'paletizadora' => $request->linea,
            'impresora' => $request->impresora,
            'impresorac' => $request->impresorac,
            'activa' => $request->activa,
        ]);

        return redirect()->route('impresoras.index')->with('success', 'Impresora creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $linea = ModelsImpresoras::findOrFail($id);
        return view('impresoras.show', compact('linea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $linea = ModelsImpresoras::findOrFail($id);
        return view('impresoras.edit_modal', compact('linea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $linea = ModelsImpresoras::findOrFail($id);

        $validated = $request->validate([
            'linea'        => 'required|string|max:10',
            'Producto'     => 'nullable|string|max:10',
            'activa'       => 'required|string|max:1',
            'impresora'    => 'nullable|string|max:30',
            'tipo_imp'     => 'nullable|string|max:10',
            'paletizadora' => 'nullable|string|max:2',
            'impresorac'   => 'nullable|string|max:30',
            'tipo_imp2'    => 'nullable|string|max:10',
            'num_imp'      => 'nullable|integer',
        ]);

        $linea->update($validated);
        return redirect()->route('impresoras.index')->with('success', 'Línea actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $linea = ModelsImpresoras::findOrFail($id);
        $linea->delete();
        return redirect()->route('impresoras.index')->with('success', 'Línea eliminada correctamente.');
    }

    public function aplicarImpresora(Request $request)
    {
        $request->validate([
                'impresora' => 'required|string',
                'impresorac' => 'required|string',
                'lineas' => 'required|array',
            ]);

            ModelsImpresoras::whereIn('orden', $request->lineas)->update([
                'impresora' => $request->impresora,
                'impresorac' => $request->impresorac,
            ]);

            return redirect()->back()->with('success', 'Impresora actualizada correctamente en las líneas seleccionadas.');
    }


}
