@push('styles')
<style>
    input[readonly],
    textarea[readonly],
    select[readonly] {
        background-color: #e9ecef; /* Gris claro */
        color: #6c757d;            /* Texto gris */
        cursor: not-allowed;       /* Cursor de prohibido */
    }
</style>
@endpush

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ implode(', ', $errors->all()) }}
        </div>
    @endif
    <form method="POST" action="{{ route('reportes.update', $data['uma']) }}">
        @csrf

        <div class="row">
            {{-- Campos no editables --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">UMA</label>
                <input type="text" class="form-control" value="{{ $data['uma'] }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Material</label>
                <input type="text" class="form-control" value="{{ $data['material'] }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Lote</label>
                <input type="text" class="form-control" value="{{ $data['lote'] }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Centro</label>
                <input type="text" class="form-control" value="{{ $data['centro'] }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Almacén</label>
                <input type="text" class="form-control" value="{{ $data['almacen'] }}" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Paletizadora</label>
                <input type="text" class="form-control" value="{{ $data['paletizadora'] }}" readonly>
            </div>

            {{-- Campos editables --}}
            <div class="col-md-4 mb-3">
                <label class="form-label">N° Orden Prev.</label>
                <input type="text" class="form-control" name="NOrdPrev" value="{{ $data['NOrdPrev'] }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Versión F</label>
                <input type="text" class="form-control" name="VersionF" value="{{ $data['VersionF'] }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha" value="{{ \Carbon\Carbon::parse($data['fecha'])->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Hora</label>
                <input type="time" class="form-control" name="hora" value="{{ \Carbon\Carbon::parse($data['hora'])->format('H:i') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control" name="cantidad" value="{{ $data['cantidad'] }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">Cancelar</button>
    </form>
</div>
