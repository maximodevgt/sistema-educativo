<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoletaController;
use App\Http\Controllers\ComportamientoController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

// Ruta nombrada "login" para que el middleware de autenticación pueda
// resolver route('login') cuando falta el token, y devuelva 401 en JSON
// en vez de lanzar un error por no encontrar una vista de login.
Route::get('login', fn () => response()->json(['message' => 'No autenticado.'], 401))->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('grados', [GradoController::class, 'index']);

    Route::apiResource('materias', MateriaController::class)
        ->except('show')
        ->parameters(['materias' => 'materia']);
    Route::apiResource('secciones', SeccionController::class)
        ->except('show')
        ->parameters(['secciones' => 'seccion']);
    Route::apiResource('maestros', MaestroController::class)->except('show');

    Route::get('actividades', [ActividadController::class, 'index']);
    Route::post('actividades', [ActividadController::class, 'store']);
    Route::put('actividades/{actividad}', [ActividadController::class, 'update']);
    Route::delete('actividades/{actividad}', [ActividadController::class, 'destroy']);

    Route::get('notas/planilla', [NotaController::class, 'planilla']);
    Route::post('notas/planilla', [NotaController::class, 'guardarPlanilla']);

    Route::get('comportamientos/planilla', [ComportamientoController::class, 'planilla']);
    Route::post('comportamientos/planilla', [ComportamientoController::class, 'guardarPlanilla']);

    Route::get('alumnos/{alumno}/boleta', [BoletaController::class, 'show']);

    Route::apiResource('alumnos', AlumnoController::class);
});
