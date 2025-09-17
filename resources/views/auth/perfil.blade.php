@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Contenido principal -->
        <!-- Contenido principal -->
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">Actualizar Datos del Perfil</div>
                <div class="card-body">
                    <!-- Mostrar mensajes de error o éxito -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Botón para actualizar perfil -->
                        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                    </form>
                </div>
            </div>
            <!-- Sección para cambiar contraseña -->
            <div class="card">
                <div class="card-header">Cambiar Contraseña</div>
                <div class="card-body">
                    <form action="{{ route('perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Contraseña actual -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Nueva contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Confirmar nueva contraseña -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                        <!-- Botón para actualizar contraseña -->
                        <button type="submit" class="btn btn-warning">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
