@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Formulario de Embalaje</h5>
                </div>

                <div class="card-body">
                    <form action="{{ url('es_lote2') }}" method="POST" name="f">
                        @csrf

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Material:</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $material }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Lote:</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $lote }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Almacén:</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $almacen }}</p>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="cantidad" class="col-sm-3 col-form-label">Cantidad:</label>
                            <div class="col-sm-9">
                                <input type="text" name="cantidad" id="cantidad" value="{{ $cantidad }}" maxlength="5" size="6" class="form-control" required>
                            </div>
                        </div>

                        {{-- Campos ocultos --}}
                        <input type="hidden" name="bloq" value="{{ $bloq }}">
                        <input type="hidden" name="sonum" value="{{ $sonum }}">
                        <input type="hidden" name="ss" value="{{ $ss }}">
                        <input type="hidden" name="lote" value="{{ $lote }}">
                        <input type="hidden" name="material" value="{{ $material }}">
                        <input type="hidden" name="descripcion" value="{{ $descripcion }}">
                        <input type="hidden" name="mat_reproceso" value="{{ $mat_reproceso }}">

                        <div class="d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-primary px-4" >Embalar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
