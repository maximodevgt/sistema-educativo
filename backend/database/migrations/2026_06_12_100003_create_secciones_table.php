<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->string('nombre');                     // A, B, C...
            $table->unsignedSmallInteger('ciclo');         // ciclo escolar, ej. 2026
            $table->foreignId('maestro_guia_id')->nullable()->constrained('maestros')->nullOnDelete();
            $table->timestamps();

            $table->unique(['grado_id', 'nombre', 'ciclo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
