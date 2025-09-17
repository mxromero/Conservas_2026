<!-- resources/views/Configuracion/editar.blade.php -->

<form action="{{ route('configuracion.update', $lineas ) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Row 1 -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="paletizadora" class="form-label">Línea</label>
            <input type="text" class="form-control" id="paletizadora" name="paletizadora"
                   value="{{ $lineas }}" readonly>
        </div>

        <div class="col-md-4 mb-3">
            <label for="NOrdPrev" class="form-label">Orden Prev</label>
            <input type="text" class="form-control" id="NOrdPrev_{{ $configuracion->paletizadora ?? $lineas  }}" name="NOrdPrev"
                   value="{{ $configuracion->NOrdPrev ?? '' }}">
        </div>

        <div class="col-md-4 mb-3">
            <label for="fecha" class="form-label">Fecha Producción</label>
            <input type="date" class="form-control" id="fecha_{{ $configuracion->paletizadora ?? $lineas  }}" name="fecha"
                   value="{{ $configuracion?->fecha ? date('Y-m-d', strtotime($configuracion->fecha)) : '' }}">
        </div>
    </div>

    <!-- Divider -->
    <div class="divider-container">
        <div class="divider-line"></div>
    </div>

    <!-- Row 2 -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="VersionF" class="form-label">Ver.Fab</label>
            <input type="text" class="form-control" id="VersionF_{{ $configuracion->paletizadora ?? $lineas  }}" name="VersionF"
                   value="{{ $configuracion->VersionF ?? '' }}">
        </div>

        <div class="col-md-4 mb-3">
            <label for="centro" class="form-label">Centro</label>
            <input type="text" class="form-control" id="centro_{{ $configuracion->paletizadora ?? $lineas  }}" name="centro"
                   value="{{ $configuracion->centro ?? '' }}" disabled>
        </div>

        <div class="col-md-4 mb-3">
            <label for="almacen" class="form-label">Almacén</label>
            <input type="text" class="form-control" id="almacen_{{ $configuracion->paletizadora ?? $lineas }}" name="almacen"
                   value="{{ $configuracion->almacen ?? '' }}">
        </div>
    </div>



    <!-- Action Buttons -->
    <div class="mt-3">
        <button type="submit" name="grabar" id="saveChanges" class="btn btn-primary" >
            <i class="fas fa-save me-2"></i> Guardar Cambios
        </button>
        <button type="button" name="btnLimpiar" id="botonLimpiar" class="btn btn-secondary" onclick="limipiar({{ $configuracion->paletizadora ?? $lineas}})">
            <i class="fa fa-undo"></i> Limpiar
        </button>
        <button type="button" name="btnSAP" id="botonSAP" class="btn btn-secondary" onclick="consulta_op_sap({{ $configuracion->paletizadora ?? $lineas}})">
            <i class="fa fa-cloud-download me-2"></i> Importar SAP
        </button>
    </div>
</form>

<style>
    .form-control {
        padding: 2px 5px;
        height: auto;
        font-size: 0.9rem;
    }
    .form-label {
        font-weight: bold;
    }
    .divider-container {
        width: 100%;
        padding: 0 15px;
        margin-bottom: 1rem;
    }
    .divider-line {
        height: 1px;
        background-color: #333338;
        width: 100%;
        display: block;
    }
</style>

