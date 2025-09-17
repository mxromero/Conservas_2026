@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 100%;">
    <div class="row">
        <!-- Contenido principal -->
        <div class="col-md-12" style="padding: 0;">
            <h2>Reporte Producción Diario</h2>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Fecha Desde</label>
                    <input type="date" id="fecha_desde" class="form-control" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label>Fecha Hasta</label>
                    <input type="date" id="fecha_hasta" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label>Paletizadora</label>
                    <select id="paletizadora" class="form-control">
                        <option value="">-- Todas --</option>
                        @foreach($paletizadoras as $p)
                            <option value="{{ $p['paletizadora'] }}">{{ $p['paletizadora'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Orden Prev.</label>
                    <select id="orden_previsional" class="form-control">
                        <option value="">-- Todas --</option>
                        @foreach($ordenes as $o)
                            <option value="{{ $o['NOrdPrev'] }}">{{ $o['NOrdPrev'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Material</label>
                    <select id="material" class="form-control">
                        <option value="">-- Todos --</option>
                        @foreach($materiales as $m)
                            <option value="{{ $m['material_orden'] }}">{{ $m['material_orden'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button id="btnBuscar" class="btn btn-primary mb-3">Buscar</button>
            <button id="btnExportar" class="btn btn-success mb-3">Exportar a Excel</button>

            <div id="resultado" class="table-responsive">
                <!-- Aquí se cargará el resultado -->
            </div>
        </div>
        <!-- Modal -->
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
</div>
@endsection

@push('styles')
<style>
    .table-responsive {
        width: 100%;
    }
    .table {
        width: 100%;
        min-width: 1600px; /* Aumenta este valor según el espacio necesario */
    }
    .container {
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
    .table td:last-child, .table th:last-child {
        width: 200px;
        white-space: nowrap;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin-right: 2px;
    }
</style>
@endpush

@push('scripts')
<script>
    function cargarResultados(page) {
        const data = {
            fecha_desde: document.getElementById('fecha_desde').value,
            fecha_hasta: document.getElementById('fecha_hasta').value,
            paletizadora: document.getElementById('paletizadora').value,
            orden_previsional: document.getElementById('orden_previsional').value,
            material: document.getElementById('material').value,
            page: page,
            _token: '{{ csrf_token() }}'
        };

        fetch(`{{ route('produccion.filtrar') }}?page=${page}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            let paginationHTML = data.last_page > 1 ? renderPagination(data) : '';
            let html = paginationHTML;
            const perPage = data.per_page ?? 25;
            let x = (page - 1) * perPage;
            if(x === 0){x = x + 1;}
            html += `
            <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Uma</th>
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
                <tbody>`;

            if (data.data.length === 0) {
                html += '<tr><td colspan="13" class="text-center text-muted">No hay resultados</td></tr>';
            } else {
                data.data.forEach(row => {
                    const fecha = new Date(row.fecha);
                    const fechaFormateada = `${fecha.getDate().toString().padStart(2, '0')}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}-${fecha.getFullYear()}`;
                    const uma = row.uma.replace(/^0+/, '');

                    const badgeSAP = row.Exp_sap?.trim() === 'X'
                        ? '<span class="badge bg-success">✅ Exportado</span>'
                        : '<span class="badge bg-secondary">❌ No Exportado</span>';

                    let hora = row.hora.slice(0, 5);

                    html += `
                    <tr>
                        <td>${x++}</td>
                        <td>${uma}</td>
                        <td>${row.NOrdPrev.replace(/^0+/, '')}</td>
                        <td>${row.VersionF}</td>
                        <td>${row.material}</td>
                        <td>${row.lote}</td>
                        <td class="text-end">${row.cantidad}</td>
                        <td>${fechaFormateada}</td>
                        <td>${hora}</td>
                        <td>${row.paletizadora}</td>
                        <td>${badgeSAP}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" title="Ver" onclick="verDetalle(${uma})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="Imprimir" onclick="imprimir(${uma})">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="eliminar(${uma})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }

            html += '</tbody></table></div>';

            html += paginationHTML; // PAGINACIÓN ABAJO

            document.getElementById('resultado').innerHTML = html;
        });
    }

    // Botón buscar
    document.getElementById('btnBuscar').addEventListener('click', function() {
        cargarResultados(1);
    });

    // Exportar
    document.getElementById('btnExportar').addEventListener('click', function () {
        const params = new URLSearchParams({
            fecha_desde: document.getElementById('fecha_desde').value,
            fecha_hasta: document.getElementById('fecha_hasta').value,
            paletizadora: document.getElementById('paletizadora').value,
            orden_previsional: document.getElementById('orden_previsional').value,
            material: document.getElementById('material').value
        });

        const url = '{{ route("produccion.exportar") }}?' + params.toString();
        window.open(url, '_blank');
    });

    function verDetalle(uma) {
        let url = `{{ route('produccion.detalle', '') }}/${uma}`;


        document.getElementById('detalleContenido').innerHTML = '<div class="text-center text-muted">Cargando...</div>';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('detalleContenido').innerHTML = html;
                let modal = new bootstrap.Modal(document.getElementById('detalleModal'));
                modal.show();
            })
            .catch(err => {
                console.error(err);
                document.getElementById('detalleContenido').innerHTML = '<div class="text-danger">Error al cargar el detalle.</div>';
            });
    }

    function cargaDetalleSCO(uma){
        let baseUrl = "{{ url('reporteDia/detalleSCO') }}";
        let url = `${baseUrl}/${uma}/SCO`;

        document.getElementById('detalleContenido').innerHTML = '<div class="text-center text-muted">Cargando...</div>';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('detalleContenido').innerHTML = html;
                let modal = new bootstrap.Modal(document.getElementById('detalleModal'));
                modal.show();
            })
            .catch(err => {
                console.error(err);
                document.getElementById('detalleContenido').innerHTML = '<div class="text-danger">Error al cargar el detalle SCO.</div>';
            });
    }

    function imprimir(uma) {
        window.open(`{{ route('produccion.imprimir', '') }}/${uma}`, '_blank');
    }

    function eliminar(uma) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede revertir!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('produccion/uma') }}/${uma}/eliminar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Eliminado',
                            'Registro eliminado correctamente.',
                            'success'
                        );
                        cargarResultados(1);
                    } else {
                        Swal.fire(
                            'Error',
                            'Error al eliminar el registro.',
                            'error'
                        );
                    }
                })
                .catch(() => {
                    Swal.fire(
                        'Error',
                        'Error al comunicarse con el servidor.',
                        'error'
                    );
                });
            }
        });
    }

    function renderPagination(data) {
        let pag = '<nav><ul class="pagination justify-content-center mt-3">';

        if (data.current_page > 1) {
            pag += `<li class="page-item"><a class="page-link" href="#" onclick="cargarResultados(${data.current_page - 1}); return false;">«</a></li>`;
        }

        if (data.current_page > 3) {
            pag += `<li class="page-item"><a class="page-link" href="#" onclick="cargarResultados(1); return false;">1</a></li>`;
            pag += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }

        for (let i = data.current_page - 2; i <= data.current_page + 2; i++) {
            if (i >= 1 && i <= data.last_page) {
                pag += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="cargarResultados(${i}); return false;">${i}</a>
                        </li>`;
            }
        }

        if (data.current_page < data.last_page - 2) {
            pag += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            pag += `<li class="page-item"><a class="page-link" href="#" onclick="cargarResultados(${data.last_page}); return false;">${data.last_page}</a></li>`;
        }

        if (data.current_page < data.last_page) {
            pag += `<li class="page-item"><a class="page-link" href="#" onclick="cargarResultados(${data.current_page + 1}); return false;">»</a></li>`;
        }

        pag += '</ul></nav>';
        return pag;
    }
</script>
@endpush
