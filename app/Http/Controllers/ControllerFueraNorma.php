<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControllerFueraNorma extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('fuera-norma.index');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function valida(Request $request)
    {
        $material = "";
        // Validación de código de material
        $codigo = $request->input('material');

        $lista = [1 => "SCO10107",2 => "SCO10102",3 => "SCO10110",4 => "SCO10105",5 => "SCO10108",6 => "SCO10152",7 => "SCO10103",8 => "SCO10106",9 => "SCO10101",
                10 => "SCO10109",11 => "SCO10104",12 => "SCO10099",13 => "SCO10097",14 => "SCO10100",15 => "SCO10098",16 => "SCO10125",17 => "SCO10174",18 => "SCO10175",
                19 => "SCO10173",20 => "SCO10153",21 => "SCO10154",22 => "SCO10155",23 => "SCO10156",24 => "SCO10157",25 => "SCO10158",26 => "SCO10159",27 => "SCO10160",
                28 => "SCO10163",29 => "SCO10161",30 => "SCO10162",31 => "SCO10111",32 => "SCO10112",33 => "SCO10113"];

        if (in_array($codigo, $lista)) {

            //validacion en SAP de los datos del material
            //en la vista fuera-norma.lote.blade.php se cargan los datos obtenidos de SAP
            return view('fuera-norma.lote', compact('material'));
        } else {
            return response()->json(['error' => 'Material no exdiste para Fuera Norma y Reproceso.'], 422);
        }

    }


    public function procesaLote(Request $request)
    {

        dd($request->all());
        $session = session('fueraNorma');
        if (!$session) {
            return redirect()->back()->with('error', 'No hay datos de sesión para procesar el lote.');
        }

        $lote = $request->input('lote');
        $mat_reproceso = $request->input('mat_reproceso');

        // Aquí puedes agregar la lógica para procesar el lote
        // Por ejemplo, guardar en la base de datos o realizar alguna acción específica

        return redirect()->route('fuera-norma.index')->with('success', 'Lote procesado correctamente.');
    }


}
