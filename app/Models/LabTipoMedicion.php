<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTipoMedicion extends Model
{
    protected $table = 'lab_tipos_medicion';

    protected $fillable = [
        'nombre',
        'unidad',
        'categoria',
        'es_texto',
        'es_booleano',
        'tipo_campo',
    ];

    protected $casts = [
        'es_texto' => 'boolean',
        'es_booleano' => 'boolean',
    ];

    public function getIsTextAttribute(): bool
    {
        return $this->es_texto || $this->tipo_campo === 'text';
    }

    public function getIsBooleanAttribute(): bool
    {
        return $this->es_booleano || $this->tipo_campo === 'boolean';
    }

    public function mediciones()
    {
        return $this->hasMany(LabMedicion::class, 'tipo_medicion_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->mediciones()->exists()) {
                throw new \Exception('No se puede eliminar este tipo de medición porque está siendo utilizado en las configuraciones de mediciones.');
            }
        });
    }
}
