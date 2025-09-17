@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Editar Usuario
                        <a href="{{ route('configuracion.usuarios') }}" class="btn btn-secondary">
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
                        <form action="{{ route('configuracion.update.usuario', $usuario->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name', $usuario->name) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="{{ old('email', $usuario->email) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="role">Rol</label>
                                <select name="role_id" id="role_id" class="form-control" required>
                                    <option value="">-- Selecciona un rol --</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->name }}"
                                            {{ old('role', $rol->name) === $rol->name ? 'selected' : '' }}>
                                            {{ ucfirst($rol->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
