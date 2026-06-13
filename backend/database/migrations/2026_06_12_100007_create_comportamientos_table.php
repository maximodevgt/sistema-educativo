<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comportamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->unsignedTinyInteger('unidad');        // 1 a 4
            $table->decimal('nota', 5, 2)->nullable();    // valoración numérica (opcional)
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'unidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comportamientos');
    }
};
