@extends('layouts.app')

@section('content')
{{ dd($lineas) }}
<div class="container">
    <h2>Detalle de Línea</h2>
    <table class="table table-bordered">
        <tr><th>Orden</th><td>{{ $lineas->orden }}</td></tr>
        <tr><th>Línea</th><td>{{ $lineas->linea }}</td></tr>
        <tr><th>Producto</th><td>{{ $lineas->Producto }}</td></tr>
        <tr><th>Activa</th><td>{{ $lineas->activa }}</td></tr>
        <tr><th>Impresora</th><td>{{ $lineas->impresora }}</td></tr>
        <tr><th>Tipo Imp</th><td>{{ $lineas->tipo_imp }}</td></tr>
        <tr><th>Paletizadora</th><td>{{ $lineas->paletizadora }}</td></tr>
        <tr><th>Impresora C</th><td>{{ $lineas->impresorac }}</td></tr>
        <tr><th>Tipo Imp 2</th><td>{{ $lineas->tipo_imp2 }}</td></tr>
        <tr><th>Num Imp</th><td>{{ $lineas->num_imp }}</td></tr>
    </table>
    <a href="{{ route('impresoras.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
