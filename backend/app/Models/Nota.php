<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $table = 'notas';

    /** Nota mínima de aprobación. */
    public const APROBADO_MINIMO = 60;

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'unidad',
        'zona',
        'examen',
    ];

    protected $casts = [
        'zona' => 'decimal:2',
        'examen' => 'decimal:2',
        'unidad' => 'integer',
    ];

    protected $appends = ['total', 'aprobado'];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /** Punteo total de la unidad: zona (60) + examen (40). */
    public function getTotalAttribute(): float
    {
        return (float) $this->zona + (float) $this->examen;
    }

    /** Si el alumno aprobó la unidad (>= 60). */
    public function getAprobadoAttribute(): bool
    {
        return $this->total >= self::APROBADO_MINIMO;
    }
}
