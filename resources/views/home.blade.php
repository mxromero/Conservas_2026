@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menú lateral -->
        <div class="col-md-2">
            @yield('sidebar')
        </div>

        <!-- Contenido principal -->
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Indicador de Líneas Cargadas') }}</div>
                <div class="card-body">

                    <!-- Sección que se actualizará -->
                    <div class="container" id="lineas-container">
                        @include('partials.lineas_cards', ['lineas' => $lineas])
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function actualizarLineas() {
        $.ajax({
            url: "{{ route('lineas.actualizar') }}",
            type: 'GET',
            success: function(data) {
                $('#lineas-container').html(data);
            },
            error: function() {
                console.error('Error al actualizar las líneas.');
            }
        });
    }

    // Actualizar cada 30 segundos
    setInterval(actualizarLineas, 30000);
});
</script>
@endpush
