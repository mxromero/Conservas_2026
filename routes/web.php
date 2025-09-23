<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ControllerImpresoras;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Pantalla inicial
Route::get('/', function () {
    return view('auth.login');
});

//Cierre de sesión personalizado para limpiar la sesión de LDAP
Route::post('/logout', function () {
    session()->flush(); // borra todos los datos de sesión
    return redirect('/login');
})->name('logout');

// 🔹 Login personalizado con LDAP
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ============================================================
| BLOQUE 1: PERFIL (cualquier usuario autenticado)
============================================================ */
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/perfil',[App\Http\Controllers\HomeController::class, 'showRegistrationForm'])->name('perfil');
    Route::put('/profile',[App\Http\Controllers\HomeController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password',[App\Http\Controllers\HomeController::class, 'updatePassword'])->name('perfil.password');
});

/* ============================================================
| BLOQUE 2: CONFIGURACIÓN GENERAL (todos los autenticados)
============================================================ */
Route::middleware(['auth'])->group(function () {
    Route::get('/configuracion', [App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::get('/configuracion/create', [App\Http\Controllers\ConfiguracionController::class, 'create'])->name('configuracion.create');
    Route::post('/configuracion', [App\Http\Controllers\ConfiguracionController::class, 'store'])->name('configuracion.store');
    Route::get('/configuracion/{id}/edit', [App\Http\Controllers\ConfiguracionController::class, 'edit'])->name('configuracion.edit');
    Route::get('/configuracion/{id}/delete', [App\Http\Controllers\ConfiguracionController::class, 'destroy'])->name('configuracion.destroy');
    Route::put('/configuracion/{paletizadora}', [App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');
    Route::get('/configuracion/{id}/enable',[App\Http\Controllers\ConfiguracionController::class, 'enable'])->name('configuracion.enable');
    Route::get('/configuracion/{id}/disable',[App\Http\Controllers\ConfiguracionController::class, 'disable'])->name('configuracion.disable');
    Route::post('/configuracion/consulta_sap',[App\Http\Controllers\ConfiguracionController::class, 'consultaSap'])->name('configuracion.consulta_sap');
});

/* ============================================================
| BLOQUE 3: ROLES Y PERMISOS (todos los autenticados)
============================================================ */
Route::middleware(['auth'])->group(function () {
    // Roles
    Route::get('/configuracion/rol', [App\Http\Controllers\ConfiguracionController::class, 'rol'])->name('configuracion.rol');
    Route::get('/configuracion/rol/create', [App\Http\Controllers\ConfiguracionController::class, 'createRol'])->name('configuracion.create.rol');
    Route::post('/configuracion/rol', [App\Http\Controllers\ConfiguracionController::class, 'storeRol'])->name('configuracion.store.rol');
    Route::get('/configuracion/rol/{id}/edit', [App\Http\Controllers\ConfiguracionController::class, 'editRol'])->name('configuracion.edit.rol');
    Route::put('/configuracion/rol/{id}', [App\Http\Controllers\ConfiguracionController::class, 'updateRol'])->name('configuracion.update.rol');
    Route::delete('/configuracion/rol/{id}', [App\Http\Controllers\ConfiguracionController::class, 'deleteRol'])->name('configuracion.delete.rol');

    // Permisos
    Route::get('/configuracion/permisos', [App\Http\Controllers\ConfiguracionController::class, 'permisos'])->name('configuracion.permisos');
    Route::get('/configuracion/permisos/create', [App\Http\Controllers\ConfiguracionController::class, 'createPermiso'])->name('configuracion.create.permiso');
    Route::get('/configuracion/permisos/{id}/edit', [App\Http\Controllers\ConfiguracionController::class, 'editPermiso'])->name('configuracion.edit.permiso');
    Route::put('/configuracion/permisos/{id}', [App\Http\Controllers\ConfiguracionController::class, 'updatePermiso'])->name('configuracion.update.permiso');
    Route::delete('/configuracion/permisos/{id}', [App\Http\Controllers\ConfiguracionController::class, 'deletePermiso'])->name('configuracion.delete.permiso');
    Route::post('/configuracion/permisos', [App\Http\Controllers\ConfiguracionController::class, 'storePermiso'])->name('configuracion.store.permiso');

    // Usuarios
    Route::get('/configuracion/usuarios', [App\Http\Controllers\ConfiguracionController::class, 'usuarios'])->name('configuracion.usuarios');
    Route::get('/configuracion/usuarios/create', [App\Http\Controllers\ConfiguracionController::class, 'createUsuario'])->name('configuracion.create.usuario');
    Route::post('/configuracion/usuarios', [App\Http\Controllers\ConfiguracionController::class, 'storeUsuario'])->name('configuracion.store.usuario');
    Route::get('/configuracion/usuarios/{id}/edit', [App\Http\Controllers\ConfiguracionController::class, 'editUsuario'])->name('configuracion.edit.usuario');
    Route::put('/configuracion/usuarios/{id}', [App\Http\Controllers\ConfiguracionController::class, 'updateUsuario'])->name('configuracion.update.usuario');
    Route::delete('/configuracion/usuarios/{id}', [App\Http\Controllers\ConfiguracionController::class, 'deleteUsuario'])->name('configuracion.delete.usuario');
});

/* ============================================================
| BLOQUE 4: LÍNEAS E IMPRESORAS (solo Usuario/Admin)
============================================================ */
Route::middleware(['auth', 'ldap.group:UP_Conservas_Usuario'])->group(function () {
    // Líneas
    Route::get('/configuracion/lineas', [App\Http\Controllers\ConfiguracionController::class, 'lineas'])->name('configuracion.lineas');
    Route::get('/configuracion/lineas/create', [App\Http\Controllers\ConfiguracionController::class, 'createLinea'])->name('configuracion.create.linea');
    Route::post('/configuracion/lineas', [App\Http\Controllers\ConfiguracionController::class, 'storeLinea'])->name('configuracion.store.linea');
    Route::get('/configuracion/lineas/{id}/edit', [App\Http\Controllers\ConfiguracionController::class, 'editLinea'])->name('configuracion.edit.linea');
    Route::put('/configuracion/lineas/{id}', [App\Http\Controllers\ConfiguracionController::class, 'updateLinea'])->name('configuracion.update.linea');
    Route::delete('/configuracion/lineas/{id}', [App\Http\Controllers\ConfiguracionController::class, 'deleteLinea'])->name('configuracion.delete.linea');

    // Impresoras
    Route::resource('impresoras', ControllerImpresoras::class);
    Route::post('/impresoras/aplicar-impresora', [ControllerImpresoras::class, 'aplicarImpresora'])->name('impresoras.aplicarImpresora');
});

/* ============================================================
| BLOQUE 5: VACIADOS, NOTIFICACIONES, REPORTES (todos autenticados)
============================================================ */
Route::middleware(['auth'])->group(function () {
    // Vaciados
    Route::get('/vaciados', [App\Http\Controllers\ControllerVaciado::class, 'index'])->name('vaciados.index');
    Route::post('/vaciados/create', [App\Http\Controllers\ControllerVaciado::class, 'create'])->name('vaciados.create');
    Route::post('/vaciados', [App\Http\Controllers\ControllerVaciado::class, 'store'])->name('vaciados.store');

    // Notificaciones
    Route::get('/notificaciones', [App\Http\Controllers\NotificacionesController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/validar-linea/{idLinea}', [App\Http\Controllers\NotificacionesController::class, 'validarLinea'])->name('notificaciones.validar.linea');
    Route::get('/notificaciones/ver', [App\Http\Controllers\NotificacionesController::class, 'view'])->name('notificaciones.view');
    Route::post('/notificaciones/limpiar-sesion', function () {
        \App\Helpers\NotificacionSession::forget();
        return response()->noContent();
    })->name('notificaciones.limpiarSesion');
    Route::post('/notificaciones/guardar', [App\Http\Controllers\NotificacionesController::class, 'store'])->name('notificaciones.store');

    // Actualizar datos de líneas
    Route::get('/linea/{paletizadora}/datos', [App\Http\Controllers\HomeController::class, 'datos']);
    Route::get('/lineas/actualizar', [App\Http\Controllers\HomeController::class, 'actualizarLineas'])->name('lineas.actualizar');

    // Fuera de Norma
    Route::get('/fuera-norma', [App\Http\Controllers\ControllerFueraNorma::class, 'index'])->name('fuera-norma.index');
    Route::post('/fuera-norma/valida', [App\Http\Controllers\ControllerFueraNorma::class, 'valida'])->name('fuera-norma.valida');
    Route::post('/fuera-norma/lote', [App\Http\Controllers\ControllerFueraNorma::class, 'procesaLote'])->name('fuera-norma.lote');
    Route::post('/fuera-norma/guardar', [App\Http\Controllers\ControllerFueraNorma::class, 'store'])->name('fuera-norma.store');

    // Reportes
    Route::get('/reporteDia', [App\Http\Controllers\ControllerReporteDiario::class, 'index'])->name('reporteDia.index');
    Route::post('/produccion/filtro', [App\Http\Controllers\ControllerReporteDiario::class, 'filtrar'])->name('produccion.filtrar');
    Route::get('/produccion/exportar', [App\Http\Controllers\ControllerReporteDiario::class, 'exportar'])->name('produccion.exportar');
    Route::get('/produccion/uma/{uma}', [App\Http\Controllers\ControllerReporteDiario::class, 'detalleUma'])->name('produccion.detalle');
    Route::get('/produccion/uma/{uma}/imprimir', [App\Http\Controllers\ControllerReporteDiario::class, 'imprimirUma'])->name('produccion.imprimir');
    Route::delete('/produccion/uma/{uma}/eliminar', [App\Http\Controllers\ControllerReporteDiario::class, 'eliminarUma'])->name('produccion.eliminar');
    Route::post('/produccion/uma/{uma}/update', [App\Http\Controllers\ControllerReporteDiario::class, 'DetalleUmaUpdate'])->name('reportes.update');
    Route::get('/reporteDia/detalleSCO/{uma}/SCO', [App\Http\Controllers\ControllerReporteDiario::class, 'detalleSCO'])->name('reporteDia.detalleSCO');
    Route::get('/trazabilidad', [App\Http\Controllers\ControllerReporteConsumo::class, 'trazabilidad'])->name('reporteDia.trazabilidad');
    Route::get('/trazabilidad/consumos', [App\Http\Controllers\ControllerReporteConsumo::class, 'filtrar'])->name('reportes.consumos');
    Route::get('/vaciado/produccion', [App\Http\Controllers\ControllerReporteVaciProd::class, 'index'])->name('reportes.vaciado_produccion');
    Route::get('/vaciado/produccion/filtro', [App\Http\Controllers\ControllerReporteVaciProd::class, 'filtrar'])->name('reportes.vaciado_produccion.filtrar');
});
