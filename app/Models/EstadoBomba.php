<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoBomba extends Model
{
    protected $table = 'estado_bombas';

    protected $fillable = [
        'dispositivo',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
