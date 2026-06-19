<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriaController extends Controller
{
    /** Listar todas las materias. */
    public function index()
    {
        return response()->json(
            Materia::orderBy('nombre')->withCount('notas')->get()
        );
    }

    /** Crear una materia. */
    public function store(Request $request)
    {
        return response()->json(Materia::create($this->validar($request)), 201);
    }

    /** Actualizar una materia. */
    public function update(Request $request, Materia $materia)
    {
        $materia->update($this->validar($request, $materia->id));

        return response()->json($materia);
    }

    /** Eliminar una materia (solo si no tiene notas registradas). */
    public function destroy(Materia $materia)
    {
        if ($materia->notas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la materia ya tiene notas registradas.',
            ], 422);
        }

        $materia->delete();

        return response()->json(['mensaje' => 'Materia eliminada.']);
    }

    private function validar(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('materias', 'nombre')->ignore($ignorarId)],
        ], [
            'nombre.unique' => 'Ya existe una materia con ese nombre.',
        ]);
    }
}
