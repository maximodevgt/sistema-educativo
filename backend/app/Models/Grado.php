<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    protected $table = 'grados';

    protected $fillable = ['nombre', 'orden'];

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class);
    }
}
