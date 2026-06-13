<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->unsignedTinyInteger('unidad');         // 1 a 4
            $table->decimal('zona', 5, 2)->default(0);     // sobre 60
            $table->decimal('examen', 5, 2)->default(0);   // sobre 40
            $table->timestamps();

            // Una sola nota por alumno, materia y unidad
            $table->unique(['alumno_id', 'materia_id', 'unidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
