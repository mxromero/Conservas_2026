@props(['linea'])

@php
    // Color inicial de la barra según condición
    if ($linea->exp_sap > 0) {
        $barraColor = '#dc3545'; // rojo si hay registros no exportados
    } elseif (substr(ltrim(trim($linea->VersionF), '0'), 0, 1) === 'R') {
        $barraColor = '#EFAF2A'; // amarillo si versión R
    } else {
        $barraColor = '#19A051'; // verde por defecto
    }
@endphp

<div class="modal fade" id="modalLinea{{ $linea->paletizadora }}" tabindex="-1" aria-labelledby="modalLinea{{ $linea->paletizadora }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Barra de color arriba según condición -->
            <div style="height: 6px; background-color: {{ $barraColor }}; border-top-left-radius: .3rem; border-top-right-radius: .3rem;"></div>

            <div class="modal-header" style="background-color: #d0e0fd">
                <h5 class="modal-title" id="modalLinea{{ $linea->paletizadora }}Label">Línea {{ $linea->paletizadora }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body" style="background-color: #fff; box-shadow: inset 0 2px 4px rgba(0,0,0,0.2); transform: translateY(1px); border-radius: 4px;">
                <i class="fa fa-industry" aria-hidden="true"></i>&nbsp;<strong>Orden Previsional</strong> : {{ ltrim($linea->NOrdPrev,'0') }}<br>
                <i class="fa fa-home" aria-hidden="true"></i>&nbsp;<strong>Versión Fabricación</strong> : {{ ltrim($linea->VersionF,'0') }}<br>
                <i class="fa fa-map" aria-hidden="true"></i>&nbsp;<strong>Material Orden</strong> : {{ ltrim($linea->material_orden,'0') }}<br>
                <i class="fa fa-map" aria-hidden="true"></i>&nbsp;<strong>Ultima Uma</strong> : {{ ltrim($linea->ult_uma,'0') }}<br>
            </div>

            <div class="modal-footer" style="background-color: #d0e0fd">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Script para actualizar la modal cada 10 segundos --}}
@push('scripts')
<script>
$(document).ready(function() {
    function actualizarModal(paletizadoraId) {
        $.ajax({
            url: '/linea/' + paletizadoraId + '/datos', // ruta que devuelve JSON de la línea
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Actualizar contenido de la modal

                let bodyHtml = `
                    <i class="fa fa-industry"></i>&nbsp;<strong>Orden Previsional</strong> : ${data.NOrdPrev}<br>
                    <i class="fa fa-home"></i>&nbsp;<strong>Versión Fabricación</strong> : ${data.VersionF}<br>
                    <i class="fa fa-map"></i>&nbsp;<strong>Material Orden</strong> : ${data.material_orden}<br>
                    <i class="fa fa-map"></i>&nbsp;<strong>Ultima Uma</strong> : ${data.ult_uma}<br>
                `;
                $('#modalLinea' + paletizadoraId + ' .modal-body').html(bodyHtml);

                // Actualizar barra de color
                let barraColor = '#19A051';
                if (data.exp_sap > 0) barraColor = '#dc3545';
                else if (data.VersionF.startsWith('R')) barraColor = '#EFAF2A';
                $('#modalLinea' + paletizadoraId + ' .modal-content > div:first-child').css('background-color', barraColor);
            },
            error: function() {
                console.error('No se pudo actualizar la modal.');
            }
        });
    }

    // Activar actualización al abrir la modal
    $('#modalLinea{{ $linea->paletizadora }}').on('show.bs.modal', function () {
        actualizarModal({{ $linea->paletizadora }});

        // Opcional: si quieres que siga actualizándose mientras esté abierta
        let interval = setInterval(function() {
            actualizarModal({{ $linea->paletizadora }});
        }, 10000);

        // Limpiar intervalo al cerrar la modal
        $(this).on('hidden.bs.modal', function () {
            clearInterval(interval);
        });
    });
});
</script>
@endpush
