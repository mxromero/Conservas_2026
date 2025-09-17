@extends('layouts.app')

@section('content')
    <div class="container-fuid">
        <div class="row">
            <div class="col-md-10">
                <div id="alert-success" class="alert alert-success">
                    {{ session('success') }}
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        {{ __('Configuración Permisos') }}
                        <a href="{{ route('configuracion.create.usuario') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear nuevo usuario
                        </a>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol(es)</th>
                                <th>Fecha de creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>


                            @forelse ($usuarios as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role_name)
                                            <span class="badge bg-info text-dark">{{ $user->role_name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Sin rol</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('configuracion.edit.usuario', $user->id) }}"
                                            class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('configuracion.delete.usuario', $user->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const alert = document.getElementById("alert-success");
        if (alert) {
            setTimeout(() => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500); // Lo elimina del DOM después del fade
            }, 5000); // ⏱️ 5000 ms = 5 segundos
        }
    });
</script>
