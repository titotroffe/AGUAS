<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelemetriaDosificadora extends Model
{
    protected $table = 'telemetria_dosificadoras';

    protected $fillable = [
        'voltaje',
        'corriente',
        'tipo',
    ];

    protected $casts = [
        'voltaje'   => 'float',
        'corriente' => 'float',
    ];
}

