<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CalidadAgua extends Model
{
    use HasFactory;

    protected $table = 'calidad_aguas';

    protected $fillable = [
        'user_id',
        'lugar',
        'filtro_numero',
        'turbiedad',
        'ph',
        'cloro_residual',
        'cloro_operador',
    ];

    protected static bool $schemaChecked = false;

    protected static function booted(): void
    {
        static::ensuringCloroOperadorSchema();
    }

    public static function ensuringCloroOperadorSchema(): void
    {
        if (static::$schemaChecked) {
            return;
        }
        static::$schemaChecked = true;

        try {
            if (Schema::hasTable('calidad_aguas') && !Schema::hasColumn('calidad_aguas', 'cloro_operador')) {
                Schema::table('calidad_aguas', function (Blueprint $table) {
                    $table->string('cloro_operador', 5)->nullable()->after('cloro_residual');
                });
            }
        } catch (\Throwable $e) {
            // Ignorar si la BD no está disponible aún en tiempo de consola
        }
    }

    public function getCloroFormateadoAttribute(): string
    {
        if ($this->cloro_residual === null) {
            return '-';
        }
        $op = in_array($this->cloro_operador, ['<', '>']) ? $this->cloro_operador . ' ' : '';
        return $op . number_format((float) $this->cloro_residual, 2);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
