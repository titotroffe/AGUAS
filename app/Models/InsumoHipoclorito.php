<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoHipoclorito extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'preparacion_archivo_contramuestra',
        'cloro_activo',
        'densidad_20c',
        'observaciones'
    ];
}
