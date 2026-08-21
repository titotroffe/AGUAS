<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LabPozo extends Model {
    protected $table = 'lab_pozos';
    protected $fillable = ['nombre', 'activo', 'direccion'];
}
