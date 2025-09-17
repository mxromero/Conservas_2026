@extends('layouts.app')


@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sidebar (no cambia de lugar) -->


            <!-- Contenido principal -->
            <div class="col-md-10">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Dejar en Línea <strong>{{ $linea->numero }}</strong></h5>
                    </div>

                    <div class="card-body bg-light">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Material</label>
                                <div class="form-control">{{ $linea->material }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Descrip</label>
                                <div class="form-control">{{ $linea->descripcion }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">O. Prev</label>
                                <div class="form-control">{{ $linea->orden_previa }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Uma</label>
                                <div class="form-control">{{ $linea->uma }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mat / Lot C</label>
                                <div class="form-control">{{ $linea->mat_lote }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha</label>
                                <div class="d-flex gap-2">
                                    <div class="form-control">{{ \Carbon\Carbon::parse($linea->fecha)->format('d/m/Y') }}</div>
                                    <div class="form-control">{{ \Carbon\Carbon::parse($linea->fecha)->format('H:i:s') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cant cons</label>
                                <div class="form-control">{{ $linea->cantidad_consumida }}</div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('configuracion.vaciar.linea', $linea->id) }}" class="btn btn-success px-4">Vaciar</a>
                            <a href="{{ route('configuracion.cancelar.linea', $linea->id) }}" class="btn btn-secondary px-4">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
