<?php

namespace App\Http\Controllers;

use App\Models\ModelsVaciadoConsumo;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProduccionExport;
use App\Models\ModelsImpresoras;
use App\Models\ModelsPaletizadoras;
use App\Models\ModelsProduccion;
use App\Helpers\ZplHelper;
use App\Helpers\PrinterHelper;
use App\Models\LogRegistro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;

class ControllerReporteDiario extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paletizadoras = ModelsImpresoras::select('paletizadora')
                                            ->orderBy('orden')->get();

        $fecha = Carbon::now()->subMonth();
        $fecha_test = $fecha->subYear(1);

        $ordenes = ModelsProduccion::where('fecha', '>=', $fecha_test)
                                        ->select('NOrdPrev')
                                        ->orderBy('NOrdPrev', 'desc')
                                        ->distinct()
                                        ->get();



        $materiales  = ModelsPaletizadoras::where('material_orden', '!=', ' ')
                                        ->select('material_orden')
                                        ->get();


        // Retorna la vista con las paletizadoras y las órdenes previas
        return view('Reportes.index', compact('paletizadoras', 'ordenes','materiales'));
    }


    public function filtrar(Request $request)
    {
        $query = ModelsProduccion::query();

        // Filtros personalizados
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

        if ($request->filled('material')) {
            $query->where('produccion.material', $request->material);
        }

        // 🔍 Búsqueda global de DataTables
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('uma', 'like', "%{$searchValue}%")
                ->orWhere('NOrdPrev', 'like', "%{$searchValue}%")
                ->orWhere('material', 'like', "%{$searchValue}%")
                ->orWhere('lote', 'like', "%{$searchValue}%")
                ->orWhere('paletizadora', 'like', "%{$searchValue}%");
            });
        }

        // 📊 Total de registros antes de filtrar
        $recordsTotal = ModelsProduccion::count();
        $recordsFiltered = $query->count();

        // 🔢 Paginación de DataTables
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query
            ->orderBy('fecha', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        // 🔁 Respuesta formato DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
}


    public function exportar(Request $request)
    {
        $query = ModelsProduccion::query()
;

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

        if ($request->filled('material')) {
            $query->where('produccion.material', $request->material);
        }

        $producciones = $query->orderBy('fecha', 'desc')->get();

        $filename = "produccion_export_" . now()->format('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($producciones) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, ['UMA', 'Orden Prev.', 'Versión', 'Material', 'Lote', 'Cantidad', 'Fecha', 'Hora', 'Paletizadora', 'Exportado']);

            foreach ($producciones as $item) {
                fputcsv($file, [
                    ltrim($item->uma, '0'),
                    $item->NOrdPrev,
                    $item->VersionF,
                    $item->material,
                    $item->lote,
                    $item->cantidad,
                    $item->fecha->format('d-m-Y'),
                    $item->hora,
                    $item->paletizadora,
                    $item->Exp_sap === 'X' ? 'Sí' : 'No',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }


    public function detalleUma($uma)
    {
        $uma = str_pad($uma, 20, '0', STR_PAD_LEFT);
        $produccion = ModelsProduccion::where('uma', $uma)->first();

        if (!$produccion) {
            return response()->json(['error' => 'UMA no encontrada'], 404);
        }
        $data = $produccion;

        return view('Reportes.detalle', compact('data'));
    }

    public function imprimirUma($uma)
    {

        $umaPadded = str_pad($uma, 20, '0', STR_PAD_LEFT);
        $produccion = ModelsProduccion::where('uma', $umaPadded)->first();

        if (!$produccion) {
            return response()->json(['error' => 'UMA no encontrada'], 404);
        }

        $lineas = ModelsImpresoras::where('paletizadora', $produccion->paletizadora)
            ->select('impresora', 'impresorac')
            ->get();

        if ($lineas->isEmpty()) {
            return response()->json(['error' => 'No se encontró impresora para esta paletizadora'], 404);
        }

        // Tomar la primera impresora encontrada
        $impresoraIp = trim($lineas[0]->impresorac);
        $impresoraCompartida = trim($lineas[0]->impresora);

        // Generar el ZPL
        try{
        $zpl = ZplHelper::generarDesdePlantilla('uma_template', [
            'paletizadora' => $produccion->paletizadora,
            'hora'         => date('H:i', strtotime($produccion->hora)),
            'fecha_etiq'   => $produccion->fecha_etiq ? $produccion->fecha_etiq->format('d-m-Y') : '',
            'cantidad'     => $produccion->cantidad,
            'fecha'        => $produccion->fecha ? $produccion->fecha->format('d-m-Y') : '',
            'uma_numero'   => (float)$produccion->uma,
            'lote'         => $produccion->lote,
            'material'     => $produccion->material,
            'descripcion'  => $produccion->descripcion,
            'uma_barcode'  => substr((float)$produccion->uma, 0, 12) . '>6' . substr((float)$produccion->uma, -1)
        ]);

        }catch(\Exception $e){
            dd($e->getMessage());
        }

        // Intentar imprimir
           $resultado = PrinterHelper::imprimir($zpl, $impresoraIp, $impresoraCompartida);

        if (!$resultado) {
            return response()->json(['error' => 'No se pudo enviar la impresión'], 500);
        }

        $user = auth()->user()->name;

        //Registra la impresión en tabla de logs 
        \DB::table('log_registros')->insert([
            'usuario' => $user,
            'accion' => 'Re-impresión',
            'modelo' => 'Produccion',
            'datos_anteriores' => json_encode($produccion),
            'datos_nuevos' => null,
            'registro_id' => $produccion->uma,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Impresión enviada correctamente',
        ]);
    }

    public function eliminarUma(Request $request, $uma)
    {
        $uma = str_pad($uma, 20, '0', STR_PAD_LEFT);
        $produccion = ModelsProduccion::where('uma', $uma)->first();

        if (!$produccion) {
            return response()->json(['success' => false, 'error' => 'UMA no encontrada'], 404);
        }
        $produccion->delete();

        return response()->json(['success' => true, 'message' => 'UMA eliminada correctamente']);
    }

    public function DetalleUmaUpdate(Request $request, $uma)
    {
        // Validar datos
        $validated = $request->validate([
            'NOrdPrev'   => 'required|string|max:20',
            'VersionF'   => 'required|string|max:10',
            'cantidad'   => 'required|numeric|min:0',
            'fecha'      => 'required|date',
            'hora'       => 'required|date_format:H:i',
        ]);

        // Normalizar UMA a 20 caracteres con ceros
        $uma = str_pad($uma, 20, '0', STR_PAD_LEFT);

        // Buscar registro
        $updateProduccion = ModelsProduccion::where('uma', $uma)->first();

        if (!$updateProduccion) {
            return response()->json(['error' => 'UMA no encontrada'], 404);
        }

        // Asignar valores validados
        $updateProduccion->NOrdPrev  = $validated['NOrdPrev'];
        $updateProduccion->VersionF  = $validated['VersionF'];
        $updateProduccion->cantidad  = $validated['cantidad'];
        $updateProduccion->fecha     = Carbon::parse($validated['fecha'])->startOfDay();
        $updateProduccion->hora      = $validated['hora'];
        $updateProduccion->Exp_sap   = ' ';

        $updateProduccion->save();

        // Redirigir a la misma vista con mensaje de éxito
        return redirect()->route('reporteDia.index')->with('success', 'UMA actualizada correctamente');

    }

    public function detalleSCO($uma)
    {
        $uma = str_pad($uma, 20, '0', STR_PAD_LEFT);
        $detalles = ModelsVaciadoConsumo::where('uma_prod', $uma)->get()->toArray();

        if (!$detalles) {
            return response()->json(['error' => 'UMA no encontrada'], 404);
        }

        $umaPrd = ltrim($uma, '0');


        return view('Reportes.detalle_sco', compact('detalles','umaPrd'));
    }

}
