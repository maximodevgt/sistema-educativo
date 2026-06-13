<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = ['grado_id', 'nombre', 'ciclo', 'maestro_guia_id'];

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    public function maestroGuia(): BelongsTo
    {
        return $this->belongsTo(Maestro::class, 'maestro_guia_id');
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }
}
