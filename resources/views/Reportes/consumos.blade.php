@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 100%;">
    <div class="row">
        <div class="col-md-12" style="padding: 0;">
            <h2>Detalle de Producción Consumos</h2>

            <!-- Filtros o controles (opcional, puedes ajustarlo) -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Fecha desde</label>
                    <input type="date" id="fecha_desde"  class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label>Fecha hasta</label>
                    <input type="date" id="fecha_hasta"  class="form-control" value="{{ now()->format('Y-m-d') }}">
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
                <div class="col-md-3">
                    <button id="btnBuscar" class="btn btn-primary mt-4">Buscar</button>
                </div>
            </div>

            <div id="resultado" class="table-responsive">

            </div>

            <!-- Modal -->
            <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detalleModalLabel">Detalle</h5>
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
</div>
@endsection

@push('styles')
<style>
    .table-responsive {
        width: 100%;
    }
    .table {
        width: 100%;
        min-width: 1800px; /* Ajustado para más columnas */
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

    function cargarResultados(pagina = 1) {
        const params = new URLSearchParams({
            fecha_desde: document.getElementById('fecha_desde').value,
            fecha_hasta: document.getElementById('fecha_hasta').value,
            paletizadora: document.getElementById('paletizadora').value,
            orden_previsional: document.getElementById('orden_previsional').value,
            page: pagina
        });

        const headerText = {
            uma          : 'Uma',
            paletizadora : 'Paletizadora',
            material     : 'Material',
            lote         : 'Lote',
            fecha        : 'Fecha',
            hora         : 'Hora',
            versionf     : 'Versión Fab.',
            NordPrev     : 'N° Ord. Prev.',
            cantidad     : 'Cantidad',
            mat_orden    : 'Material Orden',
            uma_prod     : 'Uma Prod.',
        };

        fetch(`{{ route('reportes.consumos') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(result => {
            const data = result.data ?? result;   // si tu backend envía {data, total, last_page, current_page}
            const currentPage = result.current_page ?? 1;
            const lastPage = result.last_page ?? 1;

            if (!data || !data.length) {
                document.getElementById('resultado').innerHTML =
                    '<div class="alert alert-warning">No se encontraron datos.</div>';
                return;
            }

            // Función para dibujar la paginación
            function renderPagination(current, last) {
                let pagHtml = '<nav><ul class="pagination justify-content-center">';

                // Botón anterior
                pagHtml += `
                    <li class="page-item ${current === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="cargarResultados(${current - 1}); return false;">Anterior</a>
                    </li>
                `;

                // Números de página (simple: 1 ... N)
                for (let i = 1; i <= last; i++) {
                    pagHtml += `
                        <li class="page-item ${i === current ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="cargarResultados(${i}); return false;">${i}</a>
                        </li>
                    `;
                }

                // Botón siguiente
                pagHtml += `
                    <li class="page-item ${current === last ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="cargarResultados(${current + 1}); return false;">Siguiente</a>
                    </li>
                `;

                pagHtml += '</ul></nav>';
                return pagHtml;
            }

            // Construir tabla
            let html = renderPagination(currentPage, lastPage); // paginación arriba

                html += '<table class="table table-bordered table-hover table-striped table-sm text-center align-middle">';
                html += '<thead class="table-dark"><tr>';

                html += '<th>#</th>';
            Object.keys(headerText).forEach(key => {
                html += `<th>${headerText[key]}</th>`;
            });

            html += '</tr></thead><tbody>';

            const perPage = result.per_page ?? 25;
            let x = (currentPage - 1) * perPage;
            data.forEach(item => {
                x++;
                html += '<tr>';
                html += `<td>${x}</td>`;
                Object.keys(headerText).forEach(key => {
                    let value = item[key] ?? '';
                    if (!isNaN(value) && value !== '') {
                        html += `<td class="text-end">${value}</td>`;
                    } else if (key === 'hora') {
                        html += `<td class="text-center">${value}</td>`;
                    }else if (key === 'fecha') {
                        const fecha = new Date(value);
                        const fechaFormateada = `${fecha.getDate().toString().padStart(2, '0')}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}-${fecha.getFullYear()}`;
                        html += `<td class="text-center">${fechaFormateada}</td>`;
                    } else {
                        html += `<td class="text-start">${value}</td>`;
                    }
                });
                html += '</tr>';
            });

            html += '</tbody></table>';

            html += renderPagination(currentPage, lastPage); // paginación abajo

            document.getElementById('resultado').innerHTML = html;
        })
        .catch(error => {
            console.error('Error detallado:', error.message);
            document.getElementById('resultado').innerHTML =
                '<div class="alert alert-danger">Error al cargar los datos.</div>';
        });
    }

    // Asociar al botón buscar
    document.getElementById('btnBuscar').addEventListener('click', function() {
        cargarResultados(1);
    });
</script>
@endpush

