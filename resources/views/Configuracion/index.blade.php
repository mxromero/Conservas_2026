@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Columna del contenido principal -->
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Configuración Líneas Productivas') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <div class="table-responsive">
                        <table class="table table-bordered table-hover w-100" style="background-color: #FFFFCC;">
                            <thead class="table-light">
                                <tr>
                                    <th>Linea</th>
                                    <th>Orden Prev</th>
                                    <th>Fecha Producción</th>
                                    <th>Ver.Fab</th>
                                    <th>Centro</th>
                                    <th>Almacen</th>
                                    <th>Material orden</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                           <tbody>
                                @for ($i = 1; $i <= $totalLineas; $i++)

                                    @php
                                        // Buscar si esta línea existe en la colección de paletizodoras
                                        $linea = $paletizodoras->first(function ($p) use ($i) {
                                            return (int) $p->paletizadora === $i;
                                        });
                                         $configuracion = $paletizodoras->firstWhere('paletizadora', $linea->paletizadora ?? $i);
                                    @endphp
                                    <tr>

                                        <td>{{ $i }}</td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="NordPrev"
                                                value="{{ $linea ? ltrim($linea->NOrdPrev,'0') : '' }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="fecha"
                                                value="{{ $linea && $linea->fecha ? date('d/m/Y', strtotime($linea->fecha)) : '' }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="versionF"
                                                value="{{ $linea->VersionF ?? '' }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="centro"
                                                value="{{ $linea->centro ?? '' }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="almacen"
                                                value="{{ $linea->almacen ?? '' }}"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                name="material_orden"
                                                value="{{ $linea->material_orden ?? '' }}"
                                                readonly>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-around">
                                                <button type="button"
                                                        class="btn btn-info btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditar{{ $i }}"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>

                                                <!-- Modal de Editar -->
                                                <div class="modal fade" id="modalEditar{{ $i }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-info-circle me-2"></i>Edición Línea {{ $i }}
                                                                </h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                @include('Configuracion.editar', ['configuracion' => $configuracion, 'lineas' => $i])
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-2"></i> Cerrar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    /* Estilos adicionales para que se parezca a la imagen */
    .table {
        background-color: #FFFFCC;
    }
    .form-control {
        padding: 2px 5px;
        height: auto;
        font-size: 0.9rem;
    }
    .table td, .table th {
        padding: 0.3rem;
        vertical-align: middle;
    }
    .btn-sm {
        padding: 0.1rem 0.3rem;
        font-size: 0.7rem;
    }
</style>
@endpush
<script>

    function consulta_op_sap(linea) {
        try {

            //Limpiamos modal
            limipiar(linea);
            //Dejamos curso en input Orden Previsional
            document.getElementById('NOrdPrev_' + linea).focus();
            // Primero, obtenemos la referencia a la modal correcta
            const modalId = 'modalEditar' + linea;
            const modal = document.getElementById(modalId);

            // Verificamos si la modal existe
            if (!modal) {
                console.error('No se encontró la modal con ID:', modalId);
                return;
            }

            // Obtener el botón de SAP y el botón de guardar cambios
            // dentro de la modal
            id_OrdenPrevi = 'NOrdPrev_' + linea;
            var botonSAP = document.getElementById('botonSAP');
            var saveChanges = document.getElementById('saveChanges');
            var nordPrev = document.getElementById(id_OrdenPrevi);
            // Cambiar el texto del botón a "Cargando..."
            botonSAP.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Cargando...';
            botonSAP.disabled = true;

            // Obtener el token CSRF del meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Realizar la consulta a SAP con el método POST
            fetch('/configuracion/consulta_sap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken // Incluir el token CSRF en las cabeceras
                },
                body: JSON.stringify({ NordPrev: nordPrev.value }) // Enviar el valor de NordPrev en el cuerpo de la petición
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                   // Mapear los datos de la respuesta a los campos del formulario
                    const sapData = data.data;

                    //Validamos los campos SAP antes de seguir
                if (!validarDatosSAP(sapData)) {
                    saveChanges.disabled = true;
                    return;
                }
                    // Función auxiliar para asignar valor de forma segura (dentro de la modal)
                    function setFieldValue(fieldId, value) {
                        const field = modal.querySelector('#' + fieldId);
                        if (field) {
                            field.value = value;
                        } else {
                            console.warn('No se encontró el campo con ID: ' + fieldId + ' dentro de la modal');
                        }
                    }

                    // Asignar valores a los campos correspondientes de forma segura
                    if (sapData.WPlnum) setFieldValue('NOrdPrev_' + linea, sapData.WPlnum);
                    if (sapData.WWerks) setFieldValue('centro_' + linea, sapData.WWerks);
                    if (sapData.WLgort) setFieldValue('almacen_' + linea, sapData.WLgort);
                    if (sapData.WMatnr) setFieldValue('material_orden_' + linea, sapData.WMatnr);

                    // Agregar la fecha actual al campo fecha
                    const hoy = new Date();
                    const fechaFormateada = hoy.getFullYear() + '-' +
                                            String(hoy.getMonth() + 1).padStart(2, '0') + '-' +
                                            String(hoy.getDate()).padStart(2, '0');

                    setFieldValue('fecha_' + linea, fechaFormateada);

                    //muestra mensaje si todo es correcto
                    cargarDatosCorrectamente();

                    // Habilitar botón de guardar
                    saveChanges.disabled = false;

                } else {
                        Swal.fire({
                        icon: 'error',
                        title: 'Error de Carga',
                        html: `Error al cargar los datos de SAP.<br><br>` +
                              `Por favor, verifique la orden en SAP y vuelva a intentarlo.<br><br>` +
                              `Error: ${error.message}`,
                        confirmButtonText: 'Entendido'
                    });
                }
            })
            .catch(error => {
                    Swal.fire({
                    icon: 'error',
                    title: 'Excepción en SAP',
                    html: `Error al realizar la consulta con SAP.<br><br>` +
                        `Por favor, verifique la conexión a SAP y vuelva a intentarlo.<br><br>` +
                        `Error: ${error.message}`,
                    confirmButtonText: 'Entendido'
                });
            })
            .finally(() => {
                // Restaurar el texto del botón y habilitarlo nuevamente
                botonSAP.innerHTML = '<i class="fa fa-cloud-download me-2"></i> Importar SAP';
                botonSAP.disabled = false;
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error en Servicio Web',
                html: `Por favor, verifique la conexión a SAP y vuelva a intentarlo.<br><br>` +
                      `Error: ${error.message}`,
                footer: '<a href="https://www.sap.com/latinamerica/products/what-is-sap.html" target="_blank">¿Qué es SAP?</a>',
                confirmButtonText: 'Entendido'
            });
        }
    }

    function limipiar(paletizadoraId){
            const campos = [
            'fecha',
            'VersionF',
            'centro',
            'almacen',
            'material_orden',
        ];

        campos.forEach(campo => {
            const input = document.getElementById(`${campo}_${paletizadoraId}`);
            if (input) {
                input.value = '';
            }
        });
    }

    function validarDatosSAP(sapData) {
        const camposRequeridos = {
            'WPlnum': 'Orden Previsional',
            'WWerks': 'Centro',
            'WLgort': 'Almacén',
            'WMatnr': 'Material Orden',
        };

        const faltantes = [];

        for (const campo in camposRequeridos) {
            if (!sapData[campo] || sapData[campo].toString().trim() === '') {
                faltantes.push(camposRequeridos[campo]);
            }
        }

        if (faltantes.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error en la Orden Previsional',
                html: `Faltan los siguientes campos desde SAP:<br><strong>${faltantes.join(', ')}</strong><br><br>Verifique la orden en SAP.`,
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        return true;
    }

    function cargarDatosCorrectamente() {
        // Muestra un SweetAlert que se cierra automáticamente después de 2 segundos
        Swal.fire({
            icon: 'success',
            title: '¡Datos cargados correctamente!',
            showConfirmButton: false,  // No muestra el botón de confirmar
            timer: 2000  // El mensaje se cerrará después de 2 segundos
        });
    }

 </script>
