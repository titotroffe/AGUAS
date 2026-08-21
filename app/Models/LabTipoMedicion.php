<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabTipoMedicion extends Model {
    protected $table = 'lab_tipos_medicion';
    protected $fillable = ['nombre', 'unidad'];
}
