<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabValor extends Model {
    protected $table = 'lab_valores';
    protected $fillable = ['fecha', 'medicion_id', 'valor', 'observaciones', 'user_id'];
    
    public function medicion() { return $this->belongsTo(LabMedicion::class, 'medicion_id'); }
    
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
