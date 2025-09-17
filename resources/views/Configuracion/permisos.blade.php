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
                    <div class="card-header d-flex justify-content-between align-items-center">{{ __('Configuración Permisos') }}
                        <a href="{{ route('configuracion.create.permiso') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Permiso
                        </a></div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif


            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Guard</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permisos as $permiso)
                                <tr>
                                    <td>{{ $permiso->id }}</td>
                                    <td>{{ $permiso->name }}</td>
                                    <td>{{ $permiso->guard_name }}</td>
                                    <td>
                                        <a href="{{ route('configuracion.edit.permiso', $permiso->id) }}"
                                            class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('configuracion.delete.permiso', $permiso->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este permiso?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No hay permisos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const alert = document.getElementById("alert-danger");
        if (alert) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500); // Lo elimina del DOM después del fade
            }, 5000); // ⏱️ 5000 ms = 5 segundos
        }
    });
</script>
