<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratorioAguaCruda extends Model
{
    use HasFactory;
    
    // Explicit table name to override pluralization (laboratorio_agua_crudas) if desired, but default is fine.
    protected $table = 'laboratorio_agua_cruda';

    protected $fillable = [
        'fecha',
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
        'bacterias_aerobicas_heterotrofas',
        'pseudomona_aeruginosa',
        'giardia_lamblia',
        'fitoplancton_zooplancton',
        'observaciones'
    ];
}
