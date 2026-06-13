<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\SeccionController;
use Illuminate\Support\Facades\Route;

Route::get('secciones', [SeccionController::class, 'index']);

Route::apiResource('alumnos', AlumnoController::class);
