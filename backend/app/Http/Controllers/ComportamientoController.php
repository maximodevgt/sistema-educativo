<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Comportamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComportamientoController extends Controller
{
    /** Niveles válidos de comportamiento. */
    public const NIVELES = ['Excelente', 'Muy Bueno', 'Bueno', 'Necesita mejorar'];

    /** Planilla: alumnos de una sección con su valoración para una unidad. */
    public function planilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
        ]);

        $alumnos = Alumno::where('seccion_id', $datos['seccion_id'])
            ->orderBy('apellidos')->orderBy('nombres')
            ->get();

        $comps = Comportamiento::where('unidad', $datos['unidad'])
            ->whereIn('alumno_id', $alumnos->pluck('id'))
            ->get()->keyBy('alumno_id');

        $planilla = $alumnos->map(fn ($a) => [
            'alumno_id' => $a->id,
            'codigo' => $a->codigo,
            'nombre_completo' => $a->nombre_completo,
            'valoracion' => $comps->get($a->id)?->valoracion,
            'observacion' => $comps->get($a->id)?->observacion,
        ]);

        return response()->json([
            'niveles' => self::NIVELES,
            'alumnos' => $planilla,
        ]);
    }

    /** Guardar la planilla de comportamiento. */
    public function guardarPlanilla(Request $request)
    {
        $datos = $request->validate([
            'seccion_id' => ['required', 'exists:secciones,id'],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'comportamientos' => ['required', 'array'],
            'comportamientos.*.alumno_id' => ['required', 'exists:alumnos,id'],
            'comportamientos.*.valoracion' => ['nullable', Rule::in(self::NIVELES)],
            'comportamientos.*.observacion' => ['nullable', 'string', 'max:255'],
        ]);

        $guardadas = 0;

        DB::transaction(function () use ($datos, &$guardadas) {
            foreach ($datos['comportamientos'] as $fila) {
                $valoracion = $fila['valoracion'] ?? null;
                $observacion = $fila['observacion'] ?? null;

                if ($valoracion === null && ($observacion === null || $observacion === '')) {
                    Comportamiento::where('alumno_id', $fila['alumno_id'])
                        ->where('unidad', $datos['unidad'])->delete();
                    continue;
                }

                Comportamiento::updateOrCreate(
                    ['alumno_id' => $fila['alumno_id'], 'unidad' => $datos['unidad']],
                    ['valoracion' => $valoracion, 'observacion' => $observacion]
                );
                $guardadas++;
            }
        });

        return response()->json([
            'mensaje' => "Se guardaron {$guardadas} registro(s).",
            'guardadas' => $guardadas,
        ]);
    }
}
