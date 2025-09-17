{{-- resources/views/detalle_sco.blade.php --}}
<h3 class="mb-3">📦  {{ $umaPrd }}</h3>
<table class="table table-bordered table-striped table-sm align-middle">
    <thead class="table-dark">
        <tr>
            <th>Uma</th>
            <th>Material</th>
            <th>Lote</th>
            <th>Cant.</th>
            <th>Lin.</th>
            <th>O Prev</th>
            <th>Fecha</th>
            <th>Hora</th>
       <!-- <th>Cons</th> -->
        </tr>
    </thead>
    <tbody>
        @forelse((array) $detalles as $detalle)
            <tr>
                <td>{{ $detalle['uma'] ?? '' }}</td>
                <td>{{ $detalle['material'] ?? '' }}</td>
                <td>{{ $detalle['lote'] ?? '' }}</td>
                <td class="text-end">{{ number_format((int) $detalle['cantidad'], 0, ',', '.') }}</td>
                <td>{{ $detalle['paletizadora'] ?? '' }}</td>
                <td>{{ $detalle['NordPrev'] ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($detalle['fecha'])->format('d-m-Y') }}</td>
                <td>{{ trim($detalle['hora']) }}</td>
        <!--    <td>{{ trim($detalle['consumo']) }}</td> -->
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted">No hay datos para mostrar</td>
            </tr>
        @endforelse
    </tbody>
</table>
