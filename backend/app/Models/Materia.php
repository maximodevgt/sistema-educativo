<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $table = 'materias';

    protected $fillable = ['nombre'];

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }
}
