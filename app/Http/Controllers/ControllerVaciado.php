<?php
namespace App\Http\Controllers;

use App\Helpers\VaciadoSession;
use App\Models\ModelsPaletizadoras;
use App\Models\ModelsVaciados;
use App\Services\SapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerVaciado extends Controller
{

    protected $sapService;
    public function __construct(SapService $sapService)
    {
        $this->sapService = $sapService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('Vaciados.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $sucessArray = [];
        $datosVaciado = VaciadoSession::all();
        VaciadoSession::forget(); // Limpiar la sesión después de obtener los datos

        dd($datosVaciado);

        //Realiza Movimiento hacia BM05
        $datos = [
            'WTipo' => 'M',
            'WUmaC' => $datosVaciado['uma'],
        ];
        $datos['WUmac'] = str_pad($datos['WUmaC'], 20, '0', STR_PAD_LEFT);
        $returnMover = $this->sapService->Vaciado_Mueve_BM05($datos);
        if (isset($returnMover["WDOCUMENTO"])) {
            return back()->withErrors(['Error al realizar el vaciado: ' . $returnMover['TiMensaje']]);
        }
        $datos = null;

        //Desasigna UMA
        $datos = [
            'WTipo' => 'D',
            'WUmaC' => $datosVaciado['uma'],
            'WAlmacen' => $datosVaciado['almacen'],
            'WfechaC' => $datosVaciado['fecha'],
            'WhoraC' => $datosVaciado['hora'],
            'Wversion' => $datosVaciado['version'],
            'WCantConsumo' => $datosVaciado['cant_consumo'],
            'WOp' => $datosVaciado['orden'],
            'WLinea' => $datosVaciado['paletizadora'],
        ];
        $returnDesasignar = $this->sapService->desasignar_uma($datos);
        if (isset($returnDesasignar["WDOCUMENTO"])) {
            return back()->withErrors(['Error al realizar la desasignación: ' . $returnDesasignar['TiMensaje']]);
        }else{
            // Actualiza el campo n_doc_asig en la tabla VACIADO
            $actualizados = ModelsVaciados::where('n_doc_asig', '')
                                           ->where('uma', $datosVaciado['uma'])
                                            ->where('fecha', $datosVaciado['fecha'])
                                            ->where('hora', $datosVaciado['hora'])
                                            ->update(['n_doc_asig' => $returnDesasignar["WDOCUMENTO"]]);

                                if ($actualizados === 0) {
                                    // No se actualizó ninguna fila
                                    return back()->withErrors([
                                        'No se encontró ningún registro que coincida para actualizar en la tabla VACIADO.'
                                    ]);
                                }
                $sucessArray = [
                    'Paso 1' => 'Movimiento a BM05 realizado correctamente',
                ];
        }

        //Cambio Lote
        $datos = null;
        $datos = [
            'WTipo' => 'C',
            'WUmaC' => $datosVaciado['uma'],
            'WfechaC' => $datosVaciado['fecha'],
            'WhoraC' => $datosVaciado['hora'],
            'WOp' => $datosVaciado['orden'],
            'WLinea' => $datosVaciado['paletizadora'],
            'WloteC' => $datosVaciado['charg_consumo'],
        ];

        $returnCambioLote = $this->sapService->Cambio_Lote($datos);
        if (isset($returnCambioLote["WDOCUMENTO"])) {
            return back()->withErrors(['Error al realizar el cambio de lote: ' . $returnCambioLote['TiMensaje']]);
        } else {
            // Actualiza el campo n_doc_asig en la tabla VACIADO
            $actualizados = ModelsVaciados::where('n_doc_trasp', '=', '')
                                           ->where('uma','=', $datosVaciado['uma'])
                                            ->where('fecha','=', $datosVaciado['fecha'])
                                            ->where('hora', '=',$datosVaciado['hora'])
                                            ->update(['n_doc_trasp' => $returnCambioLote["WDOCUMENTO"]]);

                                if ($actualizados === 0) {
                                    // No se actualizó ninguna fila
                                    return back()->withErrors([
                                        'No se encontró ningún registro que coincida para actualizar en la tabla VACIADO.'
                                    ]);
                                }
                $sucessArray = [
                    'Paso 2' => 'Cambio de lote realizado correctamente',
                ];
        }

        //Mueve a BU05
        $datos = null;
        $datos = [
            'WTipo' => 'MB',
            'WUmaC' => $datosVaciado['uma'],
            'WfechaC' => $datosVaciado['fecha'],
            'WhoraC' => $datosVaciado['hora'],
            'WOp' => $datosVaciado['orden'],
            'WLinea' => $datosVaciado['paletizadora'],
            'WVersion' => $datosVaciado['version'],
        ];

        $returnMoverBU05 = $this->sapService->Vaciado_Mueve_BU05($datos);
        if (isset($returnMoverBU05["WDOCUMENTO"])) {
            return back()->withErrors(['Error al realizar el movimiento a BU05: ' . $returnMoverBU05['TiMensaje']]);
        } else {
            // Actualiza el campo n_doc_asig en la tabla VACIADO
            $actualizados = ModelsVaciados::where('n_doc_des', '=', '')
                                           ->where('uma','=', $datosVaciado['uma'])
                                            ->where('fecha','=', $datosVaciado['fecha'])
                                            ->where('hora', '=',$datosVaciado['hora'])
                                            ->update(['n_doc_des' => $returnMoverBU05["WDOCUMENTO"]]);

                                if ($actualizados === 0) {
                                    // No se actualizó ninguna fila
                                    return back()->withErrors([
                                        'No se encontró ningún registro que coincida para actualizar en la tabla VACIADO.'
                                    ]);
                                }
                $sucessArray = [
                    'Paso 3' => 'Movimiento a BU05 realizado correctamente',
                ];
        }

        //Desembala UMA
        $datos = null;
        $datos = [
            'WTipo' => 'HU',
            'WUmaC' => $datosVaciado['uma'],
            'WfechaC' => $datosVaciado['fecha'],
            'WhoraC' => $datosVaciado['hora'],
        ];

        $returnDesembalar = $this->sapService->Desembalar_uma($datos);
        if (isset($returnDesembalar["WDOCUMENTO"])) {
            return back()->withErrors(['Error al realizar el desembalaje: ' . $returnDesembalar['TiMensaje']]);
        } else {
            // Actualiza el campo n_doc_asig en la tabla VACIADO
            $actualizados = ModelsVaciados::where('desembala', '=', '')
                                           ->where('uma','=', $datosVaciado['uma'])
                                            ->where('fecha','=', $datosVaciado['fecha'])
                                            ->where('hora', '=',$datosVaciado['hora'])
                                            ->update(['desembala' => 'X']);

                                if ($actualizados === 0) {
                                    // No se actualizó ninguna fila
                                    return back()->withErrors([
                                        'No se encontró ningún registro que coincida para actualizar en la tabla VACIADO.'
                                    ]);
                                }
                $sucessArray = [
                    'Paso 4' => 'Desembalaje realizado correctamente',
                ];
            }

        return back()->redirect(route('vaciados.index'))->with('success', $sucessArray);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'uma'          => 'required|digits_between:13,20',
                'paletizadora' => 'required|digits_between:1,2',
            ]);

            $uma   = str_pad($request->input('uma'), 20, '0', STR_PAD_LEFT);
            $linea = $request->input('paletizadora');

            //Valida que paletizadora  este activa
        /*    $resultado = ModelsPaletizadoras::where('paletizadora', $linea)
                ->where('eliminada', '<>','X')
                ->first();

            if (empty($resultado)) {
                return back()->withErrors(['La paletizadora no existe o está desactivada, favor de revisar.']);
            }
        */
            // Validar la UMA en SAP
            $uma_Arr = ['WUma' => $uma];
            $datos   = $this->sapService->Vaciado_uma($uma_Arr);

            // Valida que TiMensaje exista y no esté vacío
            if (! isset($datos['TiMensaje']) || empty($datos['TiMensaje'])) {
                return back()->withErrors(['No se recibió mensaje del servicio SAP.']);
            }

            $mensaje = $datos['TiMensaje'];

            $partes = explode('|', $mensaje);
            $partes = array_pad($partes, 4, null); // asegura que tenga 4 elementos, los que falten serán null

            list($codigo, $texto, $material_sco, $lote_sco) = $partes;

            if ($codigo === '007') {
                $data                          = $this->datosPaletizadora($linea);
                list($total_consumo, $decimal) = explode('.', $texto);

                $datos = [
                    'material'      => $data[0]->material_orden,
                    'descripcion'   => $data[0]->Descripcion,
                    'orden'         => $data[0]->NOrdPrev,
                    'mat_lote'      => $data[0]->mat_lote,
                    'fecha'         => Carbon::now()->format('Y-m-d'),
                    'hora'          => Carbon::now()->format('H:i'),
                    'cant_consumo'  => trim($total_consumo),
                    'LtxCj'         => $data[0]->LTxCJ,
                    'uma'           => $uma,
                    'matSCO'        => $material_sco,
                    'charg_consumo' => $lote_sco,
                    'almacen'       => $data[0]->almacen,
                    'version'       => $data[0]->versionF,
                ];

                $conversion = (int) $data[0]->LTxCJ;

                // Verificar si la UMA tiene LTxCJ asignados
                if ($conversion == 0) {
                    return back()->withErrors(['Material ' . $data[0]->material_orden . ' falta la conversión.']);
                }

                $listaMaterial = [
                    'VgMatnr'  => $data[0]->material_orden,
                    'VgMatnrV' => $material_sco,
                    'VgPlnum'  => $data[0]->NOrdPrev,
                ];

                // Validar el material en SAP
                $ok_listaMaterial = $this->sapService->Valida_Material_CS03($listaMaterial);

                // Verificar si la validación de material fue exitosa
                if (isset($ok_listaMaterial['VgMensajes']) && $ok_listaMaterial['VgMensajes'] === 'OK') {

                    // Guardar los datos en la sesión
                    VaciadoSession::set($datos);

                    return view('vaciados.index', compact('uma', 'linea', 'datos'));

                } else {
                    // Mostrar error
                    return back()->withErrors(['Material no válido: ' . $material_sco . ', no corresponde a lista de material']);
                }

            } else {
                // Algún error ocurrió
                return back()->withErrors(["Error $codigo: $texto"]);
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'Error al conectar con el servicio SAP:',
                'Mensaje: ' . $e->getMessage(),
                'Archivo: ' . $e->getFile(),
                'Línea: ' . $e->getLine(),
            ]);
        }

    }

    public function datosPaletizadora($linea)
    {
        $resultados = ModelsPaletizadoras::join('DESCRIPCION', 'material_orden', '=', 'Material')
            ->select(
                'material_orden',
                'Descripcion',
                'NOrdPrev',
                'LTxCJ',
                'almacen',
                'versionF',
                DB::raw("CONCAT(RTRIM(LTRIM(material_orden)), '|', lote_vac) as mat_lote")
            )
            ->where('paletizadora', $linea)
            ->get();
        return $resultados;

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
