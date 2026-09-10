<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabFrecuencia extends Model
{
    use HasFactory;

    protected $table = 'lab_frecuencias';

    protected $fillable = [
        'nombre',
        'slug',
    ];

    public function mediciones()
    {
        return $this->hasMany(LabMedicion::class, 'frecuencia_id');
    }
}
