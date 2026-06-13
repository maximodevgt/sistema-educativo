<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Nota;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    /**
     * Planilla de notas: alumnos de una sección con su nota
     * para una materia y unidad específicas.
     */
    public function planilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
        ]);

        $alumnos = Alumno::where('seccion_id', $datos['seccion_id'])
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        $notas = Nota::where('materia_id', $datos['materia_id'])
            ->where('unidad', $datos['unidad'])
            ->whereIn('alumno_id', $alumnos->pluck('id'))
            ->get()
            ->keyBy('alumno_id');

        $planilla = $alumnos->map(function ($a) use ($notas) {
            $nota = $notas->get($a->id);

            return [
                'alumno_id' => $a->id,
                'codigo' => $a->codigo,
                'nombre_completo' => $a->nombre_completo,
                'zona' => $nota?->zona,
                'examen' => $nota?->examen,
            ];
        });

        return response()->json($planilla);
    }

    /**
     * Guardar (crear o actualizar) la planilla completa de notas.
     */
    public function guardarPlanilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'notas' => ['required', 'array'],
            'notas.*.alumno_id' => ['required', 'exists:alumnos,id'],
            'notas.*.zona' => ['nullable', 'numeric', 'between:0,60'],
            'notas.*.examen' => ['nullable', 'numeric', 'between:0,40'],
        ]);

        $guardadas = 0;

        foreach ($datos['notas'] as $fila) {
            $zona = $fila['zona'] ?? null;
            $examen = $fila['examen'] ?? null;

            // Si la fila viene vacía, no creamos registro
            if ($zona === null && $examen === null) {
                continue;
            }

            Nota::updateOrCreate(
                [
                    'alumno_id' => $fila['alumno_id'],
                    'materia_id' => $datos['materia_id'],
                    'unidad' => $datos['unidad'],
                ],
                [
                    'zona' => $zona ?? 0,
                    'examen' => $examen ?? 0,
                ]
            );

            $guardadas++;
        }

        return response()->json([
            'mensaje' => "Se guardaron {$guardadas} nota(s).",
            'guardadas' => $guardadas,
        ]);
    }
}
