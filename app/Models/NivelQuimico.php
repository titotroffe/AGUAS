<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelQuimico extends Model
{
    use HasFactory;

    // Agrega esta línea:
    protected $fillable = ['turno_id', 'quimico', 'tanque_principal', 'tanque_auxiliar'];
}
