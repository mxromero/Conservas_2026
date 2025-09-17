<?php

namespace App\Http\Controllers;

use App\Models\ModelsImpresoras;
use App\Models\ModelsProduccion;
use App\Models\ModelsVaciadoConsumo;
use App\Models\ModelsVaciados;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerReporteVaciProd extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paletizadoras = ModelsImpresoras::select('paletizadora')
                                            ->orderBy('orden')->get();

        $fecha = Carbon::now()->subMonth();
        $fecha_test = $fecha->subYear(5);

        $ordenes = ModelsProduccion::where('fecha', '>=', $fecha_test)
                                        ->select('NOrdPrev','fecha')
                                        ->orderBy('fecha', 'desc')
                                        ->distinct()
                                        ->get();
        // Retorna la vista con las paletizadoras y las órdenes previas
        return view('Reportes.vaciado_produccion', compact('paletizadoras', 'ordenes'));

    }

        public function filtrar(Request $request)
        {
            // ----------------------------
            // Construcción de filtros
            // ----------------------------
            $fechaDesde = $request->input('fecha_desde');
            $fechaHasta = $request->input('fecha_hasta');
            $paletizadora = $request->input('paletizadora');
            $ordenPrev = $request->input('orden_previsional');

            // ----------------------------
            // Query Producción
            // ----------------------------
            $produccion = ModelsProduccion::select(
                'uma',
                'material',
                'lote',
                'cantidad',
                'centro',
                'almacen',
                'VersionF as version',
                'NOrdPrev as orden_prev',
                'paletizadora',
                'fecha',
                'hora',
                DB::raw("'P' as tipo")
            );

            if ($fechaDesde) {
                $produccion->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $produccion->where('fecha', '<=', $fechaHasta);
            }
            if ($paletizadora) {
                $produccion->where('paletizadora', $paletizadora);
            }
            if ($ordenPrev) {
                $produccion->where('NOrdPrev', $ordenPrev);
            }

            // ----------------------------
            // Query Vaciado
            // ----------------------------
            $vaciado = ModelsVaciados::select(
                'uma',
                'material',
                'lote',
                'cantidad',
                'centro',
                'almacen',
                'version',
                'orden_prev',
                'paletizadora',
                'fecha',
                'hora',
                DB::raw("'V' as tipo")
            );

            if ($fechaDesde) {
                $vaciado->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $vaciado->where('fecha', '<=', $fechaHasta);
            }
            if ($paletizadora) {
                $vaciado->where('paletizadora', $paletizadora);
            }
            if ($ordenPrev) {
                $vaciado->where('orden_prev', $ordenPrev);
            }

            // ----------------------------
            // Query Vaciado Consumo
            // ----------------------------
            $consumo = ModelsVaciadoConsumo::select(
                'uma',
                'material',
                'lote',
                'cantidad',
                DB::raw("'PDBU' as centro"),
                DB::raw("'BU05' as almacen"),
                'versionf as version',
                'NOrdPrev as orden_prev',
                'paletizadora',
                'fecha',
                'hora',
                DB::raw("'C' as tipo")
            );

            if ($fechaDesde) {
                $consumo->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $consumo->where('fecha', '<=', $fechaHasta);
            }
            if ($paletizadora) {
                $consumo->where('paletizadora', $paletizadora);
            }
            if ($ordenPrev) {
                $consumo->where('NOrdPrev', $ordenPrev);
            }

            // ----------------------------
            // Unir las 3 queries
            // ----------------------------
            $union = $produccion->union($vaciado)->union($consumo);

            // Para poder usar orderBy y paginate en unions → subquery
            $resultados = DB::query()
                ->fromSub($union, 't')
                ->orderBy('fecha', 'asc')
                ->orderBy('hora', 'asc')
                ->paginate(25);


            return response()->json($resultados);
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
