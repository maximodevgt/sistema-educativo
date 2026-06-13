<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\BoletaController;
use App\Http\Controllers\ComportamientoController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::get('grados', [GradoController::class, 'index']);
Route::get('materias', [MateriaController::class, 'index']);

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
