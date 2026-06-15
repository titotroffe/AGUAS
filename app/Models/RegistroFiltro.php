<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroFiltro extends Model
{
    use HasFactory;

    // Agrega esta línea:
    protected $fillable = ['turno_id', 'norte_1', 'norte_2', 'norte_3', 'sur_1', 'sur_2', 'sur_3'];
}