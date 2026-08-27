<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabMedicion extends Model
{
    protected $table = 'lab_mediciones';
    protected $fillable = ['modulo_id', 'insumo_id', 'pozo_id', 'tipo_medicion_id', 'activo', 'min', 'max'];

    public function modulo() { return $this->belongsTo(LabModulo::class, 'modulo_id'); }
    public function insumo() { return $this->belongsTo(LabInsumo::class, 'insumo_id'); }
    public function pozo() { return $this->belongsTo(LabPozo::class, 'pozo_id'); }
    public function tipoMedicion() { return $this->belongsTo(LabTipoMedicion::class, 'tipo_medicion_id'); }

    public function getIsTextAttribute(): bool
    {
        return $this->tipoMedicion?->is_text ?? false;
    }

    public function getIsBooleanAttribute(): bool
    {
        return $this->tipoMedicion?->is_boolean ?? false;
    }
}
