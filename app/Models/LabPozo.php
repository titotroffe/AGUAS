<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabPozo extends Model {
    protected $table = 'lab_pozos';
    protected $fillable = ['nombre', 'activo', 'direccion'];

    public function mediciones()
    {
        return $this->hasMany(LabMedicion::class, 'pozo_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->mediciones()->exists()) {
                throw new \Exception('No se puede eliminar este pozo porque está siendo utilizado en las configuraciones de mediciones.');
            }
        });
    }
}
