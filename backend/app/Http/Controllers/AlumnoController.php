<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    /** Listar alumnos con su sección y grado. */
    public function index(Request $request)
    {
        $alumnos = Alumno::with('seccion.grado')
            ->when($request->seccion_id, fn ($q, $id) => $q->where('seccion_id', $id))
            ->when($request->buscar, function ($q, $texto) {
                $q->where(function ($sub) use ($texto) {
                    $sub->where('nombres', 'ilike', "%{$texto}%")
                        ->orWhere('apellidos', 'ilike', "%{$texto}%")
                        ->orWhere('codigo', 'ilike', "%{$texto}%");
                });
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        return response()->json($alumnos);
    }

    /** Registrar un nuevo alumno. */
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $alumno = Alumno::create($datos);

        return response()->json($alumno->load('seccion.grado'), 201);
    }

    /** Mostrar un alumno. */
    public function show(Alumno $alumno)
    {
        return response()->json($alumno->load('seccion.grado'));
    }

    /** Actualizar un alumno. */
    public function update(Request $request, Alumno $alumno)
    {
        $datos = $this->validar($request, $alumno->id);

        $alumno->update($datos);

        return response()->json($alumno->load('seccion.grado'));
    }

    /** Eliminar un alumno. */
    public function destroy(Alumno $alumno)
    {
        $alumno->delete();

        return response()->json(['mensaje' => 'Alumno eliminado.']);
    }

    /** Reglas de validación compartidas. */
    private function validar(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'codigo' => ['required', 'string', 'max:50', Rule::unique('alumnos', 'codigo')->ignore($ignorarId)],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'sexo' => ['nullable', Rule::in(['M', 'F'])],
            'fecha_nacimiento' => ['nullable', 'date'],
        ]);
    }
}
