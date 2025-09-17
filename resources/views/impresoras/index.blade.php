@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">

        <div class="col-md-9">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">Listado de Impresoras</h5>
                    <a href="#" id="btnNuevaLinea" class="btn btn-light btn-sm mt-2 mt-md-0">+ Nueva Línea</a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <!-- Texto explicativo -->
                        <div class="alert alert-info p-2 mb-3">
                            <strong>Información:</strong><small> Esta sección permite aplicar una misma impresora (nombre compartido e IP) a múltiples líneas de paletizadoras.
                            Selecciona una o más líneas usando <kbd>Ctrl</kbd> + clic y luego presiona <strong>Aplicar</strong></small>.
                        </div>

                        <form action="{{ route('impresoras.aplicarImpresora') }}" method="POST" class="mb-4">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <input type="text" name="impresora" class="form-control form-control-sm" placeholder="Impresora compartida" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="impresorac" class="form-control form-control-sm" placeholder="IP Impresora" required>
                                </div>
                                <div class="col-md-4">
                                    <select name="lineas[]" class="form-select form-select-sm" multiple required>
                                        @foreach($lineas as $linea)
                                            <option value="{{ $linea->orden }}">Paletizadora {{ $linea->orden }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted"><kbd>Ctrl</kbd> + clic para seleccionar varias líneas</small>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-success">Aplicar</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(session('success'))
                        <div  id="success-alert" class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div id="error-alert" class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div id="validation-errors" class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle table-hover">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Paletizadora</th>
                                    <th>Impresora Compartida</th>
                                    <th>Impresora IP</th>
                                    <th>Activa</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lineas as $linea)
                                    <tr>
                                        <td align="center">{{ trim($linea->paletizadora) }}</td>
                                        <td align="center">{{ trim($linea->impresora) }}</td>
                                        <td align="center">{{ trim($linea->impresorac) }}</td>
                                        <td align="center">
                                            @if(strtolower(trim($linea->activa)) === 'x')
                                                <span class="d-inline-block ms-2" style="width:12px; height:12px; background-color:green; border-radius:2px;"></span>
                                            @else
                                                <span class="d-inline-block ms-2" style="width:12px; height:12px; background-color:gray; border-radius:2px;"></span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('impresoras.edit', $linea->orden) }}"
                                            data-id="{{ $linea->orden }}"
                                            class="btn btn-warning btn-sm btn-editar-linea">
                                            Editar
                                            </a>
                                            <form action="{{ route('impresoras.destroy', $linea->orden) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminacion(this)">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.col-md-9 -->
    </div> <!-- /.row -->
</div> <!-- /.container-fluid -->
<!-- Modal para editar línea -->
<div class="modal fade" id="modalEditarLinea" tabindex="-1" aria-labelledby="modalEditarLineaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalEditarLineaLabel">Editar Línea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="contenidoModalEditar">
            <p class="text-center">Cargando...</p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal para agregar nueva línea -->
<div class="modal fade" id="modalNuevaLinea" tabindex="-1" aria-labelledby="modalNuevaLineaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalNuevaLineaLabel">Agregar Nueva Línea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="contenidoModalCrear">
            <p class="text-center">Cargando...</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Modal de edición
        $('.btn-editar-linea').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = `/impresoras/${id}/edit`;

            $('#contenidoModalEditar').html('<p class="text-center">Cargando...</p>');
            $('#modalEditarLinea').modal('show');

            $.get(url, function(data) {
                $('#contenidoModalEditar').html(data);
            }).fail(function() {
                $('#contenidoModalEditar').html('<div class="alert alert-danger">Error cargando el formulario.</div>');
            });
        });

        // Modal de creación
        $('#btnNuevaLinea').on('click', function(e) {
            e.preventDefault();
            const url = `/impresoras/create`;

            $('#contenidoModalCrear').html('<p class="text-center">Cargando...</p>');
            $('#modalNuevaLinea').modal('show');

            $.get(url, function(data) {
                $('#contenidoModalCrear').html(data);
            }).fail(function() {
                $('#contenidoModalCrear').html('<div class="alert alert-danger">Error cargando el formulario.</div>');
            });
        });


    });

        document.addEventListener("DOMContentLoaded", function () {
            ['success-alert', 'error-alert', 'validation-errors'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(() => {
                        el.style.transition = "opacity 0.5s ease";
                        el.style.opacity = 0;
                        setTimeout(() => el.remove(), 500);
                    }, 4000); // Oculta luego de 4 segundos
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modalAgregarLinea');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function () {
                    const inputLinea = modal.querySelector('input[name="linea"]');
                    if (inputLinea) {
                        inputLinea.focus();
                    }
                });
            }
        });

        function confirmarEliminacion(button) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = button.closest('form');
                    if (form) {
                        form.submit(); // Solo se ejecuta si el usuario confirma
                    }
                }
            });
        }

</script>
@endpush
@endsection


