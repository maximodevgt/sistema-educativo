<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeccionController extends Controller
{
    /** Listar secciones con su grado y maestro guía. */
    public function index()
    {
        $secciones = Seccion::with(['grado', 'maestroGuia'])
            ->withCount('alumnos')
            ->get()
            ->sortBy(fn ($s) => [$s->grado->orden, $s->nombre])
            ->values();

        return response()->json($secciones);
    }

    /** Crear una sección. */
    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $seccion = Seccion::create($datos);

        return response()->json($seccion->load(['grado', 'maestroGuia']), 201);
    }

    /** Actualizar una sección. */
    public function update(Request $request, Seccion $seccion)
    {
        $datos = $this->validar($request, $seccion->id);

        $seccion->update($datos);

        return response()->json($seccion->load(['grado', 'maestroGuia']));
    }

    /** Eliminar una sección (solo si no tiene alumnos). */
    public function destroy(Seccion $seccion)
    {
        if ($seccion->alumnos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la sección tiene alumnos. Mueve o elimina sus alumnos primero.',
            ], 422);
        }

        $seccion->delete();

        return response()->json(['mensaje' => 'Sección eliminada.']);
    }

    /** Reglas de validación. */
    private function validar(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'grado_id' => ['required', 'exists:grados,id'],
            'nombre' => [
                'required', 'string', 'max:20',
                Rule::unique('secciones')->where(fn ($q) => $q
                    ->where('grado_id', $request->grado_id)
                    ->where('ciclo', $request->ciclo)
                )->ignore($ignorarId),
            ],
            'ciclo' => ['required', 'integer', 'between:2000,2100'],
            'maestro_guia_id' => ['nullable', 'exists:maestros,id'],
        ], [
            'nombre.unique' => 'Ya existe esa sección para ese grado y ciclo.',
        ]);
    }
}
