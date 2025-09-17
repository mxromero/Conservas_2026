{{-- resources/views/impresoras/edit.blade.php --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('impresoras.update', $linea->orden) }}" method="POST">
    @csrf
    @method('PUT')
    @include('impresoras.form', ['linea' => $linea])
    <div class="mt-3 d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </div>
</form>

