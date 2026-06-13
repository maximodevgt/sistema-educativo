<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaestroController extends Controller
{
    /** Listar maestros. */
    public function index()
    {
        return response()->json(
            Maestro::orderBy('apellidos')->orderBy('nombres')->withCount('secciones')->get()
        );
    }

    /** Crear un maestro. */
    public function store(Request $request)
    {
        return response()->json(Maestro::create($this->validar($request)), 201);
    }

    /** Actualizar un maestro. */
    public function update(Request $request, Maestro $maestro)
    {
        $maestro->update($this->validar($request, $maestro->id));

        return response()->json($maestro);
    }

    /** Eliminar un maestro. */
    public function destroy(Maestro $maestro)
    {
        $maestro->delete();

        return response()->json(['mensaje' => 'Maestro eliminado.']);
    }

    private function validar(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('maestros', 'email')->ignore($ignorarId)],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
