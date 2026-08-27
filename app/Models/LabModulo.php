<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabModulo extends Model {
    protected $table = 'lab_modulos';
    protected $fillable = ['descripcion'];

    public function mediciones()
    {
        return $this->hasMany(LabMedicion::class, 'modulo_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->mediciones()->exists()) {
                throw new \Exception('No se puede eliminar este módulo porque está siendo utilizado en las configuraciones de mediciones.');
            }
        });
    }
}
