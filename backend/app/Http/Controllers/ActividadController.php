<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    /** Listar actividades de una sección, materia y unidad. */
    public function index(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
        ]);

        $actividades = Actividad::where($datos)->orderBy('id')->get();

        return response()->json($actividades);
    }

    /** Crear una actividad. */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'nombre' => ['required', 'string', 'max:80'],
            'punteo_max' => ['required', 'numeric', 'between:0,60'],
        ]);

        return response()->json(Actividad::create($datos), 201);
    }

    /** Actualizar nombre o punteo de una actividad. */
    public function update(Request $request, Actividad $actividad)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'punteo_max' => ['required', 'numeric', 'between:0,60'],
        ]);

        $actividad->update($datos);

        return response()->json($actividad);
    }

    /** Eliminar una actividad (borra también sus calificaciones). */
    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return response()->json(['mensaje' => 'Actividad eliminada.']);
    }
}
