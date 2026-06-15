<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroPresion extends Model
{
    use HasFactory;

    // Agrega esta línea:
    protected $fillable = ['turno_id', 'presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna'];
}
