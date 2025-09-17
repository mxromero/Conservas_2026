<div class="row">
    @foreach ($lineas as $linea)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 rounded-3 position-relative"
                 style="cursor: pointer; transition: transform 0.2s;"
                 data-bs-toggle="modal"
                 data-bs-target="#modalLinea{{ $linea->paletizadora }}"
                 onmouseover="this.style.transform='scale(1.05)';"
                 onmouseout="this.style.transform='scale(1)';">

                <!-- Indicador de estado -->
                <div class="position-absolute top-0 start-0 w-100" style="height: 6px; background-color: {{ strtolower($linea->activa) === 'x' ? '#19A051' : '#B0B0B0' }}; border-top-left-radius: .3rem; border-top-right-radius: .3rem;"></div>

                <div class="card-body">
                    <h5 class="card-title mb-1">Línea {{ $linea->paletizadora }}</h5>

                    <!-- Resumen rápido -->
                    <p class="card-text mb-1">
                        <i class="bi bi-list-task text-primary"></i>
                        {{ $linea->material_orden }}
                    </p>
                    <p class="card-text mb-1">
                        <i class="bi bi-box-seam text-warning"></i>
                        {{ $linea->NOrdPrev ?? '-' }}
                    </p>
                    <p class="card-text mb-1">
                        <i class="bi bi-printer-fill text-info"></i>
                        {{ (int)$linea->ult_uma ?? '-' }}
                    </p>
                    <!-- Estado visual -->
                    <span class="badge {{ strtolower($linea->eliminada) === ' ' ? 'bg-secondary' : 'bg-success' }}">
                        {{ strtolower($linea->eliminada) === ' ' ? 'Inactiva' : 'Activa' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Modal individual -->
        <x-modal-linea :linea="$linea" />
    @endforeach
</div>
