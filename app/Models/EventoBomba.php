<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoBomba extends Model
{
    protected $table = 'eventos_bombas';

    protected $fillable = [
        'dispositivo',
        'user_id',
        'encendido_at',
        'apagado_at',
        'duracion_segundos',
    ];

    protected $casts = [
        'encendido_at' => 'datetime',
        'apagado_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
