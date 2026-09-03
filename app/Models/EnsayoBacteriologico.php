<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnsayoBacteriologico extends Model
{
    use HasFactory;

    protected $table = 'ensayos_bacteriologicos';

    protected $fillable = [
        'user_id',
        'lugar',
        'filtro_numero', // En caso de usar filtros, aunque el prompt dice que para decantadores es norte/sur
        'e_coli',
        'coliformes_totales',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
