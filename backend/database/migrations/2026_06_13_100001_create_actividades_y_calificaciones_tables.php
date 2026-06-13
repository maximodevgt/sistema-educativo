<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Actividades de zona (las "columnas": Tarea 1, Trabajo en clase, etc.)
        // definidas por sección + materia + unidad.
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seccion_id')->constrained('secciones')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->unsignedTinyInteger('unidad');          // 1 a 4
            $table->string('nombre');                        // "Tarea 1", "Cuaderno"...
            $table->decimal('punteo_max', 5, 2)->default(0); // cuánto vale dentro de la zona
            $table->timestamps();
        });

        // Calificación de cada alumno en cada actividad.
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->decimal('punteo', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['actividad_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
        Schema::dropIfExists('actividades');
    }
};
