<?php

namespace App\Http\Controllers;

use App\Models\Seccion;

class SeccionController extends Controller
{
    /** Listar secciones con su grado (para selects y filtros). */
    public function index()
    {
        $secciones = Seccion::with('grado')
            ->withCount('alumnos')
            ->get()
            ->sortBy(fn ($s) => [$s->grado->orden, $s->nombre])
            ->values();

        return response()->json($secciones);
    }
}
