<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    // Agrega esta línea:
    protected $fillable = ['user_id', 'hora_inicio', 'hora_final', 'estado'];
}
