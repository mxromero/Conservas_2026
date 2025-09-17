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
                    <form method="POST" action="{{ route('configuracion.store.rol') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-w">Nombre del Rol</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="{{ old('name', $rol->name) }}" required>
                            <input type="hidden" name="guard_name" value="web">
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('configuracion.rol') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
