<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalidadAgua extends Model
{
    use HasFactory;

    protected $table = 'calidad_aguas';

    protected $fillable = [
        'user_id',
        'lugar',
        'filtro_numero',
        'turbiedad',
        'ph',
        'cloro_residual',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
