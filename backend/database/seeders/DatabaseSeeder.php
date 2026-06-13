<?php

namespace Database\Seeders;

use App\Models\Grado;
use App\Models\Materia;
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
    }
}
