<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maestro extends Model
{
    protected $table = 'maestros';

    protected $fillable = ['nombres', 'apellidos', 'email', 'telefono'];

    /** Secciones donde este maestro es guía. */
    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class, 'maestro_guia_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
