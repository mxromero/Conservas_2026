@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="row">

            @if ($errors->any())
                <div class="alert alert-danger" id="alert-success">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="col-md-10">
                <div class="card">
                    <form method="POST" action="{{ route('configuracion.store.rol') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-w">Nombre del Rol</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="guard_name" class="form-label">Guard Name</label>
                            <input type="text" class="form-control" name="guard_name" id="guard_name" value="web"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('configuracion.rol') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
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
