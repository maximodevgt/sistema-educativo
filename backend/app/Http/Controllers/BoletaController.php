<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Nota;

class BoletaController extends Controller
{
    /** Boleta de calificaciones de un alumno: materias x unidades. */
    public function show(Alumno $alumno)
    {
        $alumno->load('seccion.grado');

        $materias = Materia::orderBy('nombre')->get();

        // Notas del alumno indexadas por "materia-unidad"
        $notas = Nota::where('alumno_id', $alumno->id)
            ->get()
            ->keyBy(fn ($n) => "{$n->materia_id}-{$n->unidad}");

        $promediosMaterias = [];

        $detalle = $materias->map(function ($materia) use ($notas, &$promediosMaterias) {
            $unidades = [];
            $totales = [];

            for ($u = 1; $u <= 4; $u++) {
                $nota = $notas->get("{$materia->id}-{$u}");

                if ($nota) {
                    $total = (float) $nota->zona + (float) $nota->examen;
                    $totales[] = $total;
                    $unidades[$u] = [
                        'zona' => (float) $nota->zona,
                        'examen' => (float) $nota->examen,
                        'total' => $total,
                    ];
                } else {
                    $unidades[$u] = null;
                }
            }

            $promedio = count($totales) ? round(array_sum($totales) / count($totales), 2) : null;
            if ($promedio !== null) {
                $promediosMaterias[] = $promedio;
            }

            return [
                'materia_id' => $materia->id,
                'materia' => $materia->nombre,
                'unidades' => $unidades,
                'promedio' => $promedio,
                'aprobada' => $promedio !== null ? $promedio >= Nota::APROBADO_MINIMO : null,
            ];
        });

        $promedioGeneral = count($promediosMaterias)
            ? round(array_sum($promediosMaterias) / count($promediosMaterias), 2)
            : null;

        return response()->json([
            'alumno' => [
                'id' => $alumno->id,
                'codigo' => $alumno->codigo,
                'nombre_completo' => $alumno->nombre_completo,
                'grado' => $alumno->seccion?->grado?->nombre,
                'seccion' => $alumno->seccion?->nombre,
            ],
            'materias' => $detalle,
            'promedio_general' => $promedioGeneral,
            'aprobado_minimo' => Nota::APROBADO_MINIMO,
        ]);
    }
}
