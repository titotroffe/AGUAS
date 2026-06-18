<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroPresion extends Model
{
    use HasFactory;

    protected $table = 'registro_presiones';

    protected $fillable = ['user_id', 'presion_tanque', 'presion_planta', 'presion_falcon', 'nivel_cisterna'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
