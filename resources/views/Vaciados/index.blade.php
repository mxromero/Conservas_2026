@extends('layouts.app')



@section('content')
    <div class="container-fluid py-4">
        <div class="row">


            <!-- Contenido principal -->
            <div class="col-md-10">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Genera Vaciado</h5>
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm mt-2 mt-md-0">Volver</a>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulario de UMA y Línea -->
                        <form id="formVaciado" action="{{ route('vaciados.store') }}" method="POST" novalidate>
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-9">
                                    <label for="uma" class="form-label">UMA</label>
                                    <input type="text" name="uma" id="idUma" class="form-control"
                                           value="{{ old('uma', $uma ?? '') }}" required autofocus>
                                </div>

                                <div class="col-md-3">
                                    <label for="linea" class="form-label">Línea</label>
                                    <input type="number" name="paletizadora" id="idLinea" class="form-control"
                                           value="{{ old('paletizadora', $linea ?? '') }}">
                                </div>
                            </div>

                            <div class="d-grid d-md-flex justify-content-md-end mt-3">
                                <button type="submit" id="btnCargar" class="btn btn-success">
                                    <i class="fas fa-upload me-1"></i> Cargar
                                </button>
                            </div>
                        </form>

                        <!-- Segunda parte (visible solo si se cargaron datos) -->
                        @if (isset($datos))
                            <hr class="my-4">
                                @if (isset($datos))
                                    <span id="focoCantidad" style="display:none;"></span>
                                @endif

                            <div class="mt-4">
                                <h5 class="text-danger fw-bold mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i> Dejar en Línea {{ $linea }}
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered bg-white align-middle">
                                        <tbody>
                                            <tr>
                                                <th class="text-end text-muted w-25">Material</th>
                                                <td>{{ $datos['material'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">Descrip</th>
                                                <td>{{ $datos['descripcion'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">O. Prev</th>
                                                <td>{{ $datos['orden'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">UMA</th>
                                                <td>{{ $uma }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">Mat /Lot C</th>
                                                <td>{{ $datos['mat_lote'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">Fecha</th>
                                                <td><input type="date" name="fecha" id="fecha" value="{{ old('fecha',$datos['fecha'] ?? '')  }}" /> <span class="ms-2"><input type="time" id="hora" name="hora" class="ms-2" value="{{ old('hora',$datos['hora'] ?? '') }}"></span></td>
                                            </tr>
                                            <tr>
                                                <th class="text-end text-muted">Cant cons</th>
                                                <td><input type="text"
                                                            name="cantidad"
                                                            id="cantidadID"
                                                            inputmode="numeric"
                                                            autocomplete="off"
                                                            value="{{ old('cantidadID',$datos['cant_consumo'] ?? '') }}" ></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                                    <!-- Botón Vaciar -->
                                    <form id="vaciar-form"  action="{{ route('vaciados.create') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    <button type="button" class="btn btn-outline-danger" onclick="confirmarVaciado()">
                                        <i class="fas fa-recycle me-1"></i> Vaciar
                                    </button>

                                    <!-- Botón Cancelar -->
                                    <a href="{{ route('vaciados.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const inputUma = document.getElementById("idUma");
        const inputCantidad = document.getElementById("cantidadID");
        const btnCargar = document.getElementById("btnCargar");
        const form = document.getElementById("formVaciado");

        // Foco inicial
        if (!sessionStorage.getItem("enfocarCantidad")) {
            inputUma?.focus();
        }

        // Capturar el botón y enviar manualmente el formulario
        if (btnCargar && form) {
            btnCargar.addEventListener("click", () => {
                sessionStorage.setItem("enfocarCantidad", "1");
                form.submit();
            });
        }

        // Si hay datos y la marca está activa, enfocar cantidad
        if (sessionStorage.getItem("enfocarCantidad") && inputCantidad) {
            inputCantidad.focus();
            sessionStorage.removeItem("enfocarCantidad");
        }
    });

    function confirmarVaciado() {
        Swal.fire({
            title: '¿Vaciar la línea?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, vaciar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('vaciar-form').submit();
            }
        });
    }
</script>
@endpush


