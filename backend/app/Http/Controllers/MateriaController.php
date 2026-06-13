<?php

namespace App\Http\Controllers;

use App\Models\Materia;

class MateriaController extends Controller
{
    /** Listar todas las materias. */
    public function index()
    {
        return response()->json(Materia::orderBy('nombre')->get());
    }
}
