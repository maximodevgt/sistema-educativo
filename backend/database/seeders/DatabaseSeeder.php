<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Grado;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuario administrador inicial
        User::firstOrCreate(
            ['email' => 'admin@escuela.edu.gt'],
            ['name' => 'Administrador', 'password' => bcrypt('password')]
        );

        // Grados de primaria (1° a 6°)
        $grados = [
            ['nombre' => 'Primero', 'orden' => 1],
            ['nombre' => 'Segundo', 'orden' => 2],
            ['nombre' => 'Tercero', 'orden' => 3],
            ['nombre' => 'Cuarto',  'orden' => 4],
            ['nombre' => 'Quinto',  'orden' => 5],
            ['nombre' => 'Sexto',   'orden' => 6],
        ];
        foreach ($grados as $grado) {
            Grado::firstOrCreate(['orden' => $grado['orden']], $grado);
        }

        // Materias estándar de primaria (MINEDUC Guatemala)
        $materias = [
            'Comunicación y Lenguaje',
            'Matemáticas',
            'Medio Social y Natural',
            'Ciencias Naturales y Tecnología',
            'Ciencias Sociales y Formación Ciudadana',
            'Expresión Artística',
            'Educación Física',
            'Productividad y Desarrollo',
        ];
        foreach ($materias as $materia) {
            Materia::firstOrCreate(['nombre' => $materia]);
        }

        // Maestros guía de ejemplo
        $maestros = [
            ['nombres' => 'María José', 'apellidos' => 'López García', 'email' => 'mlopez@escuela.edu.gt'],
            ['nombres' => 'Carlos Enrique', 'apellidos' => 'Pérez Morales', 'email' => 'cperez@escuela.edu.gt'],
            ['nombres' => 'Ana Lucía', 'apellidos' => 'Ramírez Soto', 'email' => 'aramirez@escuela.edu.gt'],
        ];
        foreach ($maestros as $m) {
            Maestro::firstOrCreate(['email' => $m['email']], $m);
        }

        // Una sección "A" por cada grado (ciclo 2026)
        $ciclo = 2026;
        foreach (Grado::orderBy('orden')->get() as $i => $grado) {
            Seccion::firstOrCreate(
                ['grado_id' => $grado->id, 'nombre' => 'A', 'ciclo' => $ciclo],
                ['maestro_guia_id' => Maestro::all()->get($i % 3)?->id]
            );
        }

        // Alumnos de ejemplo
        $primeroA = Seccion::whereHas('grado', fn ($q) => $q->where('orden', 1))->where('nombre', 'A')->first();
        $segundoA = Seccion::whereHas('grado', fn ($q) => $q->where('orden', 2))->where('nombre', 'A')->first();
        $terceroA = Seccion::whereHas('grado', fn ($q) => $q->where('orden', 3))->where('nombre', 'A')->first();

        $alumnos = [
            ['codigo' => 'A-2026-001', 'nombres' => 'Sofía Elena',    'apellidos' => 'Hernández Cruz',   'sexo' => 'F', 'fecha_nacimiento' => '2019-03-12', 'seccion_id' => $primeroA?->id],
            ['codigo' => 'A-2026-002', 'nombres' => 'Diego Alejandro', 'apellidos' => 'Gómez Reyes',      'sexo' => 'M', 'fecha_nacimiento' => '2019-07-25', 'seccion_id' => $primeroA?->id],
            ['codigo' => 'A-2026-003', 'nombres' => 'Valentina',       'apellidos' => 'Castillo Mejía',   'sexo' => 'F', 'fecha_nacimiento' => '2019-01-08', 'seccion_id' => $primeroA?->id],
            ['codigo' => 'A-2026-004', 'nombres' => 'Mateo',           'apellidos' => 'Ordóñez Vásquez',  'sexo' => 'M', 'fecha_nacimiento' => '2018-11-03', 'seccion_id' => $segundoA?->id],
            ['codigo' => 'A-2026-005', 'nombres' => 'Camila Andrea',   'apellidos' => 'Santos de León',   'sexo' => 'F', 'fecha_nacimiento' => '2018-05-19', 'seccion_id' => $segundoA?->id],
            ['codigo' => 'A-2026-006', 'nombres' => 'Sebastián',       'apellidos' => 'Morales Pineda',   'sexo' => 'M', 'fecha_nacimiento' => '2018-09-14', 'seccion_id' => $segundoA?->id],
            ['codigo' => 'A-2026-007', 'nombres' => 'Isabella',        'apellidos' => 'Aguilar Tucux',    'sexo' => 'F', 'fecha_nacimiento' => '2017-02-27', 'seccion_id' => $terceroA?->id],
            ['codigo' => 'A-2026-008', 'nombres' => 'Daniel Eduardo',  'apellidos' => 'Ramos Chávez',     'sexo' => 'M', 'fecha_nacimiento' => '2017-06-30', 'seccion_id' => $terceroA?->id],
        ];
        foreach ($alumnos as $a) {
            if ($a['seccion_id']) {
                Alumno::firstOrCreate(['codigo' => $a['codigo']], $a);
            }
        }
    }
}
