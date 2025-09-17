@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">


            <div class="col-md-5 offset-md-1">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Genera Notificación</h5>
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

                        {{-- Primer Formulario: Carga de Línea --}}
                        <form id="formLinea" action="{{ route('notificaciones.view') }}" method="GET" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="idLinea" class="form-label">Línea</label>
                                    <input type="number" inputmode="numeric" name="paletizadora" id="idLinea" class="form-control"
                                        value="{{ old('paletizadora', $linea ?? '') }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="d-grid d-md-flex justify-content-md-end mt-3">
                                <button type="button" id="btnCargar" class="btn btn-success">
                                    <i class="fas fa-upload me-1"></i> Cargar
                                </button>
                            </div>
                        </form>

                        @if(isset($notif) && count($notif) > 0)
                            <hr>
                            <h5 class="mt-4">Datos cargados:</h5>
                            <ul class="list-group mb-4">
                                @foreach ($notif as $key => $value)
                                    @if ($key != 'FechaSemi')
                                        <li class="list-group-item d-flex justify-content-between">
                                            <strong>{{ $key }}</strong>
                                            <span>{{ $value }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>

                            {{-- Segundo Formulario: Guardar Notificación --}}
                            <form action="{{ route('notificaciones.store') }}" method="POST" id="formGuardar">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="FechaCodificado" class="form-label">Fecha Códificado</label>
                                        <input type="date" name="fecha_codificado" id="FechaCodificado" class="form-control"
                                            value="{{ \Carbon\Carbon::parse($notif['Fecha-Semi'])->format('Y-m-d') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="cantidad" class="form-label">Cantidad</label>
                                        <input type="number" name="cantidad" id="cantidad" inputmode="numeric" class="form-control" required autofocus>
                                    </div>
                                </div>

                                <div class="d-grid d-md-flex justify-content-md-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Guardar Notificación
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('pageshow', function(event) {
            const navType = performance.getEntriesByType("navigation")[0]?.type || '';
            if (event.persisted || navType === "back_forward") {
                fetch("{{ route('notificaciones.limpiarSesion') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                }).then(() => location.reload());
            }
        });

        $(document).ready(function() {
            const $inputLinea = $('#idLinea');
            const $btnCargar = $('#btnCargar');

            function validarYEnviarFormulario() {
                const idLinea = $inputLinea.val().trim();

                if (!idLinea) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo vacío',
                        text: 'Por favor ingrese una línea.'
                    });
                    $inputLinea.focus();
                    return;
                }

                $btnCargar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Validando...');

                $.ajax({
                    url: '/notificaciones/validar-linea/' + idLinea,
                    method: 'GET',
                    success: function(response) {
                        if (!response.activa) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Línea no activa',
                                text: 'Por favor ingrese una línea que esté habilitada.'
                            });
                            $inputLinea.val('').focus();
                        } else {
                            document.getElementById('formLinea').submit();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error validando línea',
                            text: 'Ocurrió un error validando la línea ingresada.'
                        });
                        $inputLinea.focus();
                    },
                    complete: function() {
                        $btnCargar.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Cargar');
                    }
                });
            }

            $btnCargar.on('click', validarYEnviarFormulario);

            $inputLinea.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    validarYEnviarFormulario();
                }
            });

            $inputLinea.on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            @if(isset($notif) && count($notif) > 0)
                $('#cantidad').focus();
            @else
                $inputLinea.focus();
            @endif
        });
    </script>
@endpush
