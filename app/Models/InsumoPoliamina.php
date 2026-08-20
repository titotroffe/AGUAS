<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoPoliamina extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'preparacion_archivo_contramuestra',
        'densidad_20c',
        'observaciones'
    ];
}
