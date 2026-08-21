<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabValor extends Model {
    protected $table = 'lab_valores';
    protected $fillable = ['fecha', 'medicion_id', 'valor', 'observaciones'];
    
    public function medicion() { return $this->belongsTo(LabMedicion::class, 'medicion_id'); }
}
