<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabInsumo extends Model {
    protected $table = 'lab_insumos';
    protected $fillable = ['nombre'];

    public function mediciones()
    {
        return $this->hasMany(LabMedicion::class, 'insumo_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if ($model->mediciones()->exists()) {
                throw new \Exception('No se puede eliminar este insumo porque está siendo utilizado en las configuraciones de mediciones.');
            }
        });
    }
}
