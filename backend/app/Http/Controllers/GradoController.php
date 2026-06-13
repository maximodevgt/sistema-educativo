<?php

namespace App\Http\Controllers;

use App\Models\Grado;

class GradoController extends Controller
{
    /** Listar grados ordenados (1° a 6°). */
    public function index()
    {
        return response()->json(Grado::orderBy('orden')->get());
    }
}
