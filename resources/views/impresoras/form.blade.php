@php
    $linea = $linea ?? null;
@endphp

<div class="mb-3">
    <label class="form-label">Orden</label>
    <input type="number" name="orden" class="form-control" value="{{ old('orden', $linea->orden ?? '') }}" {{ isset($linea) ? 'readonly' : '' }} required>
</div>

<div class="mb-3">
    <label class="form-label">Línea</label>
    <input type="text" name="linea" class="form-control" value="{{ old('linea', $linea->linea ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Producto</label>
    <input type="text" name="Producto" class="form-control" value="{{ old('Producto', $linea->Producto ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Activa</label>
    <input type="text" name="activa" class="form-control" value="{{ old('activa', $linea->activa ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Impresora</label>
    <input type="text" name="impresora" class="form-control" value="{{ old('impresora', $linea->impresora ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Tipo Impresora</label>
    <input type="text" name="tipo_imp" class="form-control" value="{{ old('tipo_imp', $linea->tipo_imp ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Paletizadora</label>
    <input type="text" name="paletizadora" class="form-control" value="{{ old('paletizadora', $linea->paletizadora ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Impresora C</label>
    <input type="text" name="impresorac" class="form-control" value="{{ old('impresorac', $linea->impresorac ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Tipo Impresora 2</label>
    <input type="text" name="tipo_imp2" class="form-control" value="{{ old('tipo_imp2', $linea->tipo_imp2 ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Número Imp.</label>
    <input type="number" name="num_imp" class="form-control" value="{{ old('num_imp', $linea->num_imp ?? '') }}">
</div>
