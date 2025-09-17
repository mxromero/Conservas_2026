<?php

namespace App\Http\Controllers;

use App\Models\ModelsImpresoras;
use App\Models\ModelsVaciadoConsumo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ControllerReporteConsumo extends Controller
{

    public function trazabilidad(){
        $paletizadoras = ModelsImpresoras::select('paletizadora')
                                            ->orderBy('orden')->get();

        $fecha = Carbon::now()->subMonth();
        $fecha_test = $fecha->subYear(5);

        $ordenes = ModelsVaciadoConsumo::where('fecha', '>=', $fecha_test)
                                        ->select('NOrdPrev','fecha')
                                        ->orderBy('fecha', 'desc')
                                        ->distinct()
                                        ->get();


        // Retorna la vista con las paletizadoras y las órdenes previas
        return view('Reportes.consumos', compact('paletizadoras', 'ordenes'));
    }

    public function filtrar(Request $request)
    {
        $query = ModelsVaciadoConsumo::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('paletizadora')) {
            $query->where('paletizadora', $request->paletizadora);
        }

        if ($request->filled('orden_previsional')) {
            $query->where('NOrdPrev', $request->orden_previsional);
        }

        $consumos = $query->orderBy('hora', 'asc')->paginate(25);

        return response()->json($consumos);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
}
