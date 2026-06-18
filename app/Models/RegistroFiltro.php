<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroFiltro extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3', 'inicio_lavado', 'fin_lavado'];

    protected $casts = [
        'inicio_lavado' => 'datetime',
        'fin_lavado' => 'datetime',
        'norte_1' => 'boolean',
        'norte_2' => 'boolean',
        'norte_3' => 'boolean',
        'sur_1' => 'boolean',
        'sur_2' => 'boolean',
        'sur_3' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}