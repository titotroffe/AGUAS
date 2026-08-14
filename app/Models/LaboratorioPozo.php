<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratorioPozo extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'pozo_numero',
        'coliformes_totales',
        'e_coli_coliformes',
        'observaciones'
    ];
}
