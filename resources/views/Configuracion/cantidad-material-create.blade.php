@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Agregar Nuevo Material</h5>
                    <a href="{{ route('configuracion.cantidad-material') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cantidadmaterial.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="Material" class="form-label">Material</label>
                            <input type="text" name="Material" id="Material" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="linea" class="form-label">Línea</label>
                            <input type="text" name="linea" id="linea" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="cant_pro" class="form-label">Cantidad Producción</label>
                            <input type="number" name="cant_pro" id="cant_pro" class="form-control" min="1" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
