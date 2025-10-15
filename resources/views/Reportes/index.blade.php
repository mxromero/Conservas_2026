@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 100%;">
    <div class="row">
        <div class="col-md-12" style="padding: 0;">
            <h2>Reporte Producción Diario</h2>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="fecha_desde">Fecha Desde</label>
                    <input type="date" id="fecha_desde" class="form-control" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta">Fecha Hasta</label>
                    <input type="date" id="fecha_hasta" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="paletizadora">Paletizadora</label>
                    <select id="paletizadora" class="form-control">
                        <option value="">-- Todas --</option>
                        @foreach($paletizadoras as $p)
                        <option value="{{ $p['paletizadora'] }}">{{ $p['paletizadora'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="orden_previsional">Orden Prev.</label>
                    <select id="orden_previsional" class="form-control">
                        <option value="">-- Todas --</option>
                        @foreach($ordenes as $o)
                        <option value="{{ $o['NOrdPrev'] }}">{{ $o['NOrdPrev'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="material">Material</label>
                    <select id="material" class="form-control">
                        <option value="">-- Todos --</option>
                        @foreach($materiales as $m)
                        <option value="{{ $m['material_orden'] }}">{{ $m['material_orden'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button id="btnBuscar" class="btn btn-primary mb-3">Buscar</button>


            <div id="resultado" class="table-responsive mt-3"></div>
        </div>
    </div>

    {{-- Modal detalle --}}
    <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleModalLabel">Detalle Producción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="detalleContenido">
                    <div class="text-center text-muted">Cargando...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<!-- DataTables JS -->
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
    document.addEventListener("DOMContentLoaded", function() {

        let tabla;

        document.getElementById("btnBuscar").addEventListener("click", function() {
            cargarResultados();
        });

        function cargarResultados() {
            const params = {
                fecha_desde: document.getElementById('fecha_desde').value,
                fecha_hasta: document.getElementById('fecha_hasta').value,
                paletizadora: document.getElementById('paletizadora').value,
                orden_previsional: document.getElementById('orden_previsional').value,
                material: document.getElementById('material').value,
            };

            // Destruir instancia previa
            if (tabla) {
                tabla.destroy();
                document.getElementById('resultado').innerHTML = `
                <table id="tablaProduccion" class="table table-striped table-hover table-bordered align-middle text-center" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th>UMA</th>
                            <th>Orden Prev.</th>
                            <th>Versión Fab.</th>
                            <th>Material</th>
                            <th>Lote</th>
                            <th>Cant.LT</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Paletizadora</th>
                            <th>Exportado SAP</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                </table>`;
            }

            // Inicializar DataTable con server-side
            tabla = $('#tablaProduccion').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('produccion.filtrar') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.fecha_desde = params.fecha_desde;
                        d.fecha_hasta = params.fecha_hasta;
                        d.paletizadora = params.paletizadora;
                        d.orden_previsional = params.orden_previsional;
                        d.material = params.material;
                    }
                },
                columns: [{
                        data: 'uma',
                        render: d => d.replace(/^0+/, '')
                    },
                    {
                        data: 'NOrdPrev',
                        render: d => d?.replace(/^0+/, '')
                    },
                    {
                        data: 'VersionF'
                    },
                    {
                        data: 'material'
                    },
                    {
                        data: 'lote'
                    },
                    {
                        data: 'cantidad',
                        className: 'text-end'
                    },
                    {
                        data: 'fecha',
                        render: d => new Date(d).toLocaleDateString('es-CL')
                    },
                    {
                        data: 'hora',
                        render: d => d?.slice(0, 5)
                    },
                    {
                        data: 'paletizadora'
                    },
                    {
                        data: 'Exp_sap',
                        render: d => d?.trim() === 'X' ?
                            '<span class="badge bg-success">✅ Exportado</span>' :
                            '<span class="badge bg-secondary">❌ No Exportado</span>'
                    },
                    {
                        data: 'uma',
                        orderable: false,
                        searchable: false,
                        render: uma => `
                    <button class="btn btn-sm btn-outline-primary" title="Ver" onclick="verDetalle(${uma.replace(/^0+/, '')})"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-outline-success" title="Imprimir" onclick="imprimir(${uma.replace(/^0+/, '')})"><i class="fas fa-print"></i></button>
                    <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminar(${uma.replace(/^0+/, '')})"><i class="fas fa-trash"></i></button>
                `
                    }
                ],
                order: [
                    [6, 'desc']
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: 'Excel'
                    },
                ]
            });
        }

        // Inicial carga (últimos 7 días)
        cargarResultados();
    });

    // --- Acciones ---
    function verDetalle(uma) {
        const url = `{{ route('produccion.detalle', '') }}/${uma}`;
        document.getElementById('detalleContenido').innerHTML = '<div class="text-center text-muted">Cargando...</div>';
        fetch(url).then(r => r.text()).then(html => {
            document.getElementById('detalleContenido').innerHTML = html;
            new bootstrap.Modal(document.getElementById('detalleModal')).show();
        });
    }

    function imprimir(uma) {
        window.open(`{{ route('produccion.imprimir', '') }}/${uma}`, '_blank');
    }

    function eliminar(uma) {
        if (!confirm('¿Confirma eliminar la UMA ' + uma + '?')) return;
        fetch(`{{ url('produccion/uma') }}/${uma}/eliminar`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    alert('Registro eliminado correctamente.');
                    $('#tablaProduccion').DataTable().ajax.reload();
                } else {
                    alert('Error al eliminar.');
                }
            })
            .catch(() => alert('Error de comunicación.'));
    }
</script>
@endpush