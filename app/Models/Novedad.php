<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Novedad extends Model
{
    use HasFactory;

    protected $table = 'novedades';

    protected $fillable = ['user_id', 'modulo', 'es_jefatura', 'mensaje'];

    protected $casts = [
        'es_jefatura' => 'boolean',
    ];

    protected static bool $schemaChecked = false;

    protected static function booted(): void
    {
        static::ensuringModuloSchema();
    }

    public static function ensuringModuloSchema(): void
    {
        if (static::$schemaChecked) {
            return;
        }
        static::$schemaChecked = true;

        try {
            if (Schema::hasTable('novedades')) {
                if (!Schema::hasColumn('novedades', 'modulo')) {
                    Schema::table('novedades', function (Blueprint $table) {
                        $table->string('modulo', 30)->nullable()->after('user_id')->index();
                    });

                    DB::statement("
                        UPDATE novedades n
                        INNER JOIN users u ON n.user_id = u.id
                        SET n.modulo = CASE
                            WHEN u.role = 'operador' THEN 'operadores'
                            WHEN u.role = 'quimico' THEN 'quimico'
                            WHEN u.role = 'laboratorio' THEN 'laboratorio'
                            ELSE 'operadores'
                        END
                        WHERE n.modulo IS NULL
                    ");
                }

                if (!Schema::hasColumn('novedades', 'es_jefatura')) {
                    Schema::table('novedades', function (Blueprint $table) {
                        $table->boolean('es_jefatura')->default(false)->after('modulo');
                    });
                }
            }
        } catch (\Throwable $e) {
            // Ignorar si la BD no está disponible aún en tiempo de consola
        }
    }

    public function scopeParaModulo($query, string $modulo)
    {
        static::ensuringModuloSchema();
        return $query->where('modulo', $modulo);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
