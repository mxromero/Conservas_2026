@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Agregar Nueva Línea Productiva
                        <a href="{{ route('configuracion.lineas') }}" class="btn btn-secondary">
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

                        <form action="{{ route('configuracion.store.linea') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="nombre">Nombre de la Línea</label>
                                <input type="text" name="paletizadora" id="nombre" class="form-control"
                                value="{{ old('nombre', $totalLineas) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control" required>
                                    <option value="activa" {{ old('estado') === 'activa' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactiva" {{ old('estado') === 'inactiva' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Línea
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
