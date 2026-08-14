<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratorioProductoTerminado extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'bacterias_aerobias_heterotrofas',
        'pseudomona',
        'giardia_lamblia',
        'fitoplancton_zooplancton',
        'color',
        'olor',
        'sabor',
        'turbiedad',
        'aluminio',
        'cloruro',
        'hierro',
        'ph',
        'sulfato',
        'solidos_disueltos_totales',
        'mercurio',
        'cadmio',
        'arsenico',
        'cromo',
        'observaciones'
    ];
}
