<?php
namespace App\Http\Controllers;

use App\Helpers\NotificacionSession;
use App\Models\ModelsPaletizadoras;
use App\Models\ModelsProduccion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        NotificacionSession::forget();
        return view('notificacion.index');
    }

    public function validarLinea($idLinea)
    {
        NotificacionSession::forget();

        $linea = ModelsPaletizadoras::where('paletizadora', $idLinea)->first();

        if (! $linea) {
            return response()->json(['activa' => false]);
        }

        $prod = ModelsProduccion::where('paletizadora', '=', $linea->paletizadora)
            ->where('material', '=', $linea->material_orden)
            ->orderBy('uma', 'desc')
            ->first();

        $uma = '';

        if (! $prod) {
            $planta       = env('PLANTA');
            $largo        = str_pad('', 5, '0');
            $codigo       = preg_replace('/\D/', '', trim($linea->material_orden));
            $paletizadora = $linea->paletizadora;
            $uma_base     = $planta . $codigo . $paletizadora . $largo;
            $uma          = str_pad($uma_base, 20, '0', STR_PAD_LEFT);
        } else {
            $uma = str_pad((string) ((int) $prod->uma + 1), 20, '0', STR_PAD_LEFT);
        }
        $nom = substr($linea->material_orden, 0, 3);
        $fechaString = $linea->fecha;
        $fechaCarbon = Carbon::parse($fechaString);
        $lote = $nom . $fechaCarbon->format('mY');

        $datos = [
            'Linea'     => $linea->paletizadora,
            'Orden Previsional'  => $linea->NOrdPrev,
            'Versión Fabricación'  => $linea->VersionF,
            'Material'  => trim($linea->material_orden),
            'Fecha-Semi' => Carbon::parse($linea->fecha)->format('d-m-Y'),
            'Almacen'   => $linea->almacen,
            'Uma'       => $uma,
            'Lote'    => $lote,
            'Hora' => Carbon::now()->format('H:i:s'),
            'Fecha' => Carbon::now()->format('d-m-Y'),
        ];

        NotificacionSession::set($datos);

        if ($linea) {
            return response()->json(['activa' => true]);
        }
    }

    public function view()
    {
        $notif = null;
        $notif = NotificacionSession::all();
        return view('notificacion.index', compact('notif'));
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
        try {
            $session = NotificacionSession::all();

            if (empty($session)) {
                return redirect()->back()->with('error', 'No hay datos de sesión disponibles.');
            }

            if (ModelsProduccion::where('uma', $session['Uma'])->exists()) {
                return redirect()->back()->with('error', 'Esta UMA ya fue registrada anteriormente.');
            }

            $datosNotificacion = [
                'uma'             => str_pad($session['Uma'], 20, ' ', STR_PAD_RIGHT),
                'material'        => str_pad($session['Material'], 8, ' ', STR_PAD_RIGHT),
                'lote'            => str_pad($session['Lote'], 10, ' ', STR_PAD_RIGHT),
                'centro'          => str_pad('PDBU', 4, ' ', STR_PAD_RIGHT),
                'almacen'         => str_pad($session['Almacen'], 4, ' ', STR_PAD_RIGHT),
                'NOrdPrev'        => str_pad($session['Orden Previsional'], 10, ' ', STR_PAD_RIGHT),
                'VersionF'        => str_pad($session['Versión Fabricación'], 4, ' ', STR_PAD_RIGHT),
                'fecha'           => Carbon::parse($session['Fecha'])->format('Y-m-d H:i:s'),
                'hora'            => $session['Hora'], // ya es nchar(8)
                'fecha_semi'      => Carbon::parse($session['Fecha-Semi'])->format('Y-m-d H:i:s'),
                'cantidad'        => (int)$request->input('cantidad'),
       //         'cantidad2'       => (int)$request->input('cantidad'),
                'temp'            => (int)substr(ltrim($session['Uma'], '0'), 0, 1),
                'paletizadora'    => (int)$session['Linea'],
                'fechaCodificado' => Carbon::parse($request->input('fecha_codificado'))->format('Y-m-d'),
                'n_doc'           => str_pad('', 10, ' '),
                'li_mb'           => str_pad('', 12, ' '),
                'li_fq'           => str_pad('', 12, ' '),
            ];

           // \Log::info('Insertando producción:', $datosNotificacion);

            $registro = new ModelsProduccion($datosNotificacion);
            $registro->save();

            NotificacionSession::forget();

            return redirect()->back()->with('success', 'Notificación registrada correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al guardar producción: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
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
