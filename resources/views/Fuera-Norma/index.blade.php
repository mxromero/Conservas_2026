@extends('layouts.app')

@section('title', 'Fuera Norma')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4>Fuera Norma</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fuera-norma.valida') }}">
                            @csrf

                            <div class="mb-3">
                                <input type="text" name="material" id="material" class="form-control w-25" placeholder="Ingresar Material" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
@endsection
