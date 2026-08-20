<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoSulfato extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'preparacion_archivo_contramuestra',
        'residuo_insoluble',
        'oxido_ferroso',
        'oxido_ferrico',
        'oxido_aluminio',
        'oxidos_utiles',
        'manganeso',
        'densidad_20c',
        'observaciones'
    ];
}
