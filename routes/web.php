<?php

use App\Http\Controllers\EncuestaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('encuestas.index');
});

// Rutas de encuestas
Route::get('/encuestas', [EncuestaController::class, 'index'])->name('encuestas.index');
Route::get('/encuestas/crear', [EncuestaController::class, 'create'])->name('encuestas.create');
Route::post('/encuestas', [EncuestaController::class, 'store'])->name('encuestas.store');
Route::get('/encuestas/{encuesta}', [EncuestaController::class, 'show'])->name('encuestas.show');
Route::delete('/encuestas/{encuesta}', [EncuestaController::class, 'destroy'])->name('encuestas.destroy');

// Rutas para responder
Route::get('/encuestas/{encuesta}/responder', [EncuestaController::class, 'responder'])->name('encuestas.responder');
Route::post('/encuestas/{encuesta}/responder', [EncuestaController::class, 'guardarRespuesta'])->name('encuestas.guardar-respuesta');

// Exportar
Route::get('/encuestas/{encuesta}/exportar', [EncuestaController::class, 'exportarCSV'])->name('encuestas.exportar');

// Generar datos de ejemplo
Route::post('/encuestas/generar-ejemplo', [EncuestaController::class, 'generarDatosEjemplo'])->name('encuestas.generar-ejemplo');