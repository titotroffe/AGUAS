<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caudalimetro extends Model
{
    protected $table = 'caudalimetros';

    protected $fillable = [
        'user_id',
        'bomba',
        'caudal_m3h',
    ];

    protected $casts = [
        'caudal_m3h' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
