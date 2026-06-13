<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = ['actividad_id', 'alumno_id', 'punteo'];

    protected $casts = [
        'punteo' => 'decimal:2',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
