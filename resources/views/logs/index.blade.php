@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2>Logs de Registro</h2>

    <div class="row mb-3">
        <div class="col-md-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" id="usuario" class="form-control" placeholder="Filtrar por usuario">
        </div>
        <div class="col-md-2">
            <label for="accion" class="form-label">Acción</label>
            <select id="accion" class="form-control">
                <option value="">-- Todas --</option>
                <option value="creación">Creación</option>
                <option value="actualización">Actualización</option>
                <option value="eliminación">Eliminación</option>
                <option value="Re-impresión">Re-impresión</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="modelo" class="form-label">Modelo</label>
            <input type="text" id="modelo" class="form-control" placeholder="Filtrar por modelo">
        </div>
        <div class="col-md-2">
            <label for="registro_id" class="form-label">Registro ID</label>
            <input type="text" id="registro_id" class="form-control" placeholder="Filtrar por ID">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button id="btnBuscar" class="btn btn-primary flex-grow-1">
                <i class="fas fa-search me-1"></i>Buscar
            </button>
            <button id="btnLimpiar" class="btn btn-outline-secondary">
                <i class="fas fa-undo me-1"></i>Limpiar
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaLogs" class="table table-striped table-hover" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Usuario</th>
                            <th width="10%">Acción</th>
                            <th width="10%">Modelo</th>
                            <th width="15%">Registro ID</th>
                            <th width="20%">Datos Anteriores</th>
                            <th width="20%">Datos Nuevos</th>
                            <th width="10%">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data se cargará con JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver datos completos -->
<div class="modal fade" id="modalDatos" tabindex="-1" aria-labelledby="modalDatosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDatosLabel">Detalles Completos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="contenidoModal" class="p-3 bg-light rounded" style="max-height: 500px; overflow-y: auto;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    .badge-accion {
        font-size: 0.8em;
        padding: 0.35em 0.65em;
    }
    .btn-datos {
        font-size: 0.8em;
        padding: 0.25em 0.5em;
    }
    .texto-resumido {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
    }
    table th {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
let dataTable;

function formatearBadgeAccion(accion) {
    const colores = {
        'creación': 'success',
        'actualización': 'warning',
        'eliminación': 'danger',
        'Re-impresión': 'info'
    };
    const color = colores[accion] || 'secondary';
    return `<span class="badge bg-${color} badge-accion">${accion}</span>`;
}

function formatearJSON(datos) {
    if (!datos) return '<span class="text-muted">N/A</span>';
    
    try {
        // Intentar parsear como JSON
        const objeto = typeof datos === 'string' ? JSON.parse(datos) : datos;
        const textoFormateado = JSON.stringify(objeto, null, 2);
        return `<div>
            <span class="texto-resumido" title="${textoFormateado.replace(/"/g, '&quot;')}">
                ${textoFormateado.substring(0, 100)}${textoFormateado.length > 100 ? '...' : ''}
            </span>
            <button class="btn btn-sm btn-outline-primary btn-datos ms-1" data-datos='${JSON.stringify(objeto)}'>
                <i class="fas fa-expand"></i>
            </button>
        </div>`;
    } catch (e) {
        // Si no es JSON válido, mostrar como texto plano
        return `<div>
            <span class="texto-resumido" title="${datos}">
                ${datos.substring(0, 100)}${datos.length > 100 ? '...' : ''}
            </span>
            <button class="btn btn-sm btn-outline-primary btn-datos ms-1" data-datos='${datos}'>
                <i class="fas fa-expand"></i>
            </button>
        </div>`;
    }
}

function cargarLogs() {
    const data = {
        usuario: document.getElementById('usuario').value,
        accion: document.getElementById('accion').value,
        modelo: document.getElementById('modelo').value,
        registro_id: document.getElementById('registro_id').value,
        _token: '{{ csrf_token() }}'
    };

    if (dataTable) {
        dataTable.destroy();
    }

    dataTable = $('#tablaLogs').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('logs.listar') }}",
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': data._token },
            error: function(xhr) {
                console.error('Error cargando logs:', xhr);
                alert('Error cargando los registros. Por favor, intente nuevamente.');
            }
        },
        columns: [
            { 
                data: 'id',
                className: 'text-center'
            },
            { 
                data: 'usuario',
                render: function(data) {
                    return data || '<span class="text-muted">N/A</span>';
                }
            },
            { 
                data: 'accion',
                render: formatearBadgeAccion
            },
            { 
                data: 'modelo',
                render: function(data) {
                    return data || '<span class="text-muted">N/A</span>';
                }
            },
            { 
                data: 'registro_id',
                render: function(data) {
                    return `<code>${data || 'N/A'}</code>`;
                }
            },
            { 
                data: 'datos_anteriores',
                render: formatearJSON
            },
            { 
                data: 'datos_nuevos',
                render: formatearJSON
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if (!data) return '<span class="text-muted">N/A</span>';
                    const fecha = new Date(data);
                    return `${fecha.getDate().toString().padStart(2,'0')}-${(fecha.getMonth()+1).toString().padStart(2,'0')}-${fecha.getFullYear()}<br>${fecha.getHours().toString().padStart(2,'0')}:${fecha.getMinutes().toString().padStart(2,'0')}`;
                }
            },
        ],
        dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i>Excel',
                className: 'btn btn-success'
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
            emptyTable: "No hay registros para mostrar con los filtros actuales",
            zeroRecords: "No se encontraron registros que coincidan con la búsqueda"
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        drawCallback: function(settings) {
            // Agregar event listeners a los botones de ver datos completos
            $('.btn-datos').off('click').on('click', function() {
                const datos = $(this).data('datos');
                const contenido = typeof datos === 'object' ? 
                    JSON.stringify(datos, null, 2) : 
                    datos;
                
                $('#contenidoModal').text(contenido);
                $('#modalDatos').modal('show');
            });
        }
    });
}

// Event Listeners
document.getElementById('btnBuscar').addEventListener('click', cargarLogs);
document.getElementById('btnLimpiar').addEventListener('click', function() {
    document.getElementById('usuario').value = '';
    document.getElementById('accion').value = '';
    document.getElementById('modelo').value = '';
    document.getElementById('registro_id').value = '';
    cargarLogs();
});

// Buscar al presionar Enter en cualquier filtro
['usuario', 'modelo', 'registro_id'].forEach(id => {
    document.getElementById(id).addEventListener('keypress', function(e) {
        if (e.key === 'Enter') cargarLogs();
    });
});

// Carga inicial
document.addEventListener('DOMContentLoaded', cargarLogs);
</script>
@endpush