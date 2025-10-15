<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogRegistro;

class LogRegistroController extends Controller
{
    public function index()
    {
        return view('logs.index');
    }

    public function listar(Request $request)
    {
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 25));
        $searchValue = $request->input('search.value', '');

        $query = LogRegistro::query()
            ->select('id', 'usuario', 'accion', 'modelo', 'registro_id', 'created_at', 'datos_anteriores', 'datos_nuevos');

        $totalRecords = $query->count();

        if ($searchValue) {
            $query->where(function($q) use ($searchValue) {
                $q->where('usuario', 'like', "%{$searchValue}%")
                ->orWhere('accion', 'like', "%{$searchValue}%")
                ->orWhere('modelo', 'like', "%{$searchValue}%")
                ->orWhere('registro_id', 'like', "%{$searchValue}%");
            });
        }

        $recordsFiltered = $query->count();

        $data = $query->orderBy('id', 'desc')
                    ->skip($start)
                    ->take($length)
                    ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
