<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = ['seccion_id', 'materia_id', 'unidad', 'nombre', 'punteo_max'];

    protected $casts = [
        'unidad' => 'integer',
        'punteo_max' => 'decimal:2',
    ];

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }
}
