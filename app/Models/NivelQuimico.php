<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelQuimico extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'quimico', 'tipo_tanque', 'nivel'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
