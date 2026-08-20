<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoCal extends Model
{
    use HasFactory;

    protected $table = 'insumo_cales';

    protected $fillable = [
        'fecha',
        'preparacion_archivo_contramuestra',
        'peso_litro',
        'observaciones'
    ];
}
