<form action="{{ route('impresoras.update', $linea->orden) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Campo Línea -->
    <div class="mb-3">
        <label for="linea" class="form-label">Línea</label>
        <input type="text" class="form-control" name="linea" value="{{ trim($linea->orden) }}">
    </div>
    <!-- Campo Impresora compartida-->
    <div class="mb-3">
        <label for="impresora" class="form-label">Impresora Compartida</label>
        <input type="text" class="form-control" name="impresora" value="{{ trim($linea->impresora) }}">
    </div>
    <!-- Campo impresora IP-->
    <div class="mb-3">
        <label for="impresorac" class="form-label">Impresora IP</label>
        <input type="text" class="form-control" name="impresorac" value="{{ trim($linea->impresorac) }}">
    </div>
    <!-- Campo Activo-->
    <div class="mb-3">
        <label for="activa" class="form-label">Activa</label>
        <select class="form-select" name="activa">
            <option value="X" {{ strtoupper(trim($linea->activa)) === 'X' ? 'selected' : '' }}>Sí</option>
            <option value="N" {{ strtoupper(trim($linea->activa)) === 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>


    <div class="text-end">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </div>
</form>
