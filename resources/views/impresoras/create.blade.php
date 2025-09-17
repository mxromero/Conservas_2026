<form action="{{ route('impresoras.store') }}" method="POST" id="modalAgregarLinea">
    @csrf

    <!-- Campo Línea -->
    <div class="mb-3">
        <label for="linea" class="form-label">Línea </label>
        <input type="text" class="form-control" name="linea" id="linea" value="{{ old('linea') }}" >
    </div>

    <!-- Campo Impresora compartida-->
    <div class="mb-3">
        <label for="impresora" class="form-label">Impresora Compartida</label>
        <input type="text" class="form-control" name="impresora" id="impresora" value="{{ old('impresora') }}">
    </div>

    <!-- Campo impresora IP-->
    <div class="mb-3">
        <label for="impresorac" class="form-label">Impresora IP</label>
        <input type="text" class="form-control" name="impresorac" id="impresorac" value="{{ old('impresorac') }}">
    </div>

    <!-- Campo Activo -->
    <div class="mb-3">
        <label for="activa" class="form-label">Activa</label>
        <select class="form-select" name="activa" id="activa">
            <option value="X" {{ old('activa') === 'X' ? 'selected' : '' }}>Sí</option>
            <option value="N" {{ old('activa') === 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">Crear línea</button>
    </div>
</form>
