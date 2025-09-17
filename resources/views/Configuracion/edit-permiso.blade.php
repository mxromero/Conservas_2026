@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="row">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="col-md-10">
                <div class="card">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            {{ __('Configuración Permisos') }}
                            <a href="{{ route('configuracion.create.permiso') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Permiso
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>

                    <form action="{{ route('configuracion.update.permiso', $permisos->id) }}" method="POST" class="card shadow p-4">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Nombre del Permiso</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $permisos->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="guard_name">Guard</label>
                            <input type="text" name="guard_name" id="guard_name" class="form-control" value="{{ old('guard_name', $permisos->guard_name) }}" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('configuracion.permisos') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Actualizar</button>
                        </div>
                    </form>




                </div>

            </div>
        </div>
    </div>
@endsection
