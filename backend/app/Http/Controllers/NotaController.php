<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    /** Zona máxima posible (los otros 40 son el examen). */
    private const ZONA_MAX = 60;

    /**
     * Planilla: actividades (columnas) + alumnos con sus calificaciones y examen.
     */
    public function planilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
        ]);

        $actividades = Actividad::where($datos)->orderBy('id')->get(['id', 'nombre', 'punteo_max']);

        $alumnos = Alumno::where('seccion_id', $datos['seccion_id'])
            ->orderBy('apellidos')->orderBy('nombres')
            ->get();

        $alumnoIds = $alumnos->pluck('id');

        // Calificaciones por "alumno-actividad"
        $califs = Calificacion::whereIn('actividad_id', $actividades->pluck('id'))
            ->whereIn('alumno_id', $alumnoIds)
            ->get()
            ->keyBy(fn ($c) => "{$c->alumno_id}-{$c->actividad_id}");

        // Examen guardado en notas
        $notas = Nota::where('materia_id', $datos['materia_id'])
            ->where('unidad', $datos['unidad'])
            ->whereIn('alumno_id', $alumnoIds)
            ->get()->keyBy('alumno_id');

        $filas = $alumnos->map(function ($a) use ($actividades, $califs, $notas) {
            $calificaciones = [];
            foreach ($actividades as $act) {
                $c = $califs->get("{$a->id}-{$act->id}");
                $calificaciones[$act->id] = $c ? (float) $c->punteo : null;
            }

            return [
                'alumno_id' => $a->id,
                'codigo' => $a->codigo,
                'nombre_completo' => $a->nombre_completo,
                'examen' => $notas->get($a->id)?->examen !== null ? (float) $notas->get($a->id)->examen : null,
                'calificaciones' => $calificaciones,
            ];
        });

        return response()->json([
            'actividades' => $actividades,
            'alumnos' => $filas,
        ]);
    }

    /**
     * Guardar la planilla: calificaciones por actividad + examen.
     * La zona se calcula como la suma de las calificaciones (máx 60).
     */
    public function guardarPlanilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'materia_id' => ['required', 'exists:materias,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'notas' => ['required', 'array'],
            'notas.*.alumno_id' => ['required', 'exists:alumnos,id'],
            'notas.*.examen' => ['nullable', 'numeric', 'between:0,40'],
            'notas.*.calificaciones' => ['array'],
        ]);

        // Actividades válidas de esta planilla (id => punteo_max)
        $actividades = Actividad::where([
            'seccion_id' => $datos['seccion_id'],
            'materia_id' => $datos['materia_id'],
            'unidad' => $datos['unidad'],
        ])->get()->keyBy('id');

        $guardadas = 0;

        DB::transaction(function () use ($datos, $actividades, &$guardadas) {
            foreach ($datos['notas'] as $fila) {
                $alumnoId = $fila['alumno_id'];
                $calificaciones = $fila['calificaciones'] ?? [];
                $zona = 0;

                foreach ($actividades as $actId => $act) {
                    $valor = $calificaciones[$actId] ?? null;

                    if ($valor === null || $valor === '') {
                        // Si se borró, eliminamos la calificación previa
                        Calificacion::where('actividad_id', $actId)
                            ->where('alumno_id', $alumnoId)->delete();
                        continue;
                    }

                    // No permitir más que el máximo de la actividad
                    $valor = min((float) $valor, (float) $act->punteo_max);

                    Calificacion::updateOrCreate(
                        ['actividad_id' => $actId, 'alumno_id' => $alumnoId],
                        ['punteo' => $valor]
                    );

                    $zona += $valor;
                }

                $zona = min($zona, self::ZONA_MAX);
                $examen = $fila['examen'] ?? null;

                if ($zona > 0 || $examen !== null) {
                    Nota::updateOrCreate(
                        ['alumno_id' => $alumnoId, 'materia_id' => $datos['materia_id'], 'unidad' => $datos['unidad']],
                        ['zona' => $zona, 'examen' => $examen ?? 0]
                    );
                    $guardadas++;
                } else {
                    // Sin datos: limpiar nota previa
                    Nota::where('alumno_id', $alumnoId)
                        ->where('materia_id', $datos['materia_id'])
                        ->where('unidad', $datos['unidad'])->delete();
                }
            }
        });

        return response()->json([
            'mensaje' => "Se guardaron {$guardadas} nota(s).",
            'guardadas' => $guardadas,
        ]);
    }
}
