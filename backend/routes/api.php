<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::get('secciones', [SeccionController::class, 'index']);
Route::get('materias', [MateriaController::class, 'index']);

Route::get('notas/planilla', [NotaController::class, 'planilla']);
Route::post('notas/planilla', [NotaController::class, 'guardarPlanilla']);

Route::apiResource('alumnos', AlumnoController::class);
