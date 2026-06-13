<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comportamiento extends Model
{
    protected $table = 'comportamientos';

    protected $fillable = [
        'alumno_id',
        'unidad',
        'nota',
        'observacion',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'unidad' => 'integer',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
