<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comportamientos', function (Blueprint $table) {
            // Nivel de comportamiento: Excelente / Muy Bueno / Bueno / Necesita mejorar
            $table->string('valoracion', 20)->nullable()->after('unidad');
        });
    }

    public function down(): void
    {
        Schema::table('comportamientos', function (Blueprint $table) {
            $table->dropColumn('valoracion');
        });
    }
};
