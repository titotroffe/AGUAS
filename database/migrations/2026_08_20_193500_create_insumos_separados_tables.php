<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('insumo_sulfatos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->boolean('preparacion_archivo_contramuestra')->default(false);
            $table->string('residuo_insoluble')->nullable();
            $table->string('oxido_ferroso')->nullable();
            $table->string('oxido_ferrico')->nullable();
            $table->string('oxido_aluminio')->nullable();
            $table->string('oxidos_utiles')->nullable();
            $table->string('manganeso')->nullable();
            $table->string('densidad_20c')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('insumo_hipocloritos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->boolean('preparacion_archivo_contramuestra')->default(false);
            $table->string('cloro_activo')->nullable();
            $table->string('densidad_20c')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('insumo_poliaminas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->boolean('preparacion_archivo_contramuestra')->default(false);
            $table->string('densidad_20c')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('insumo_cales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->boolean('preparacion_archivo_contramuestra')->default(false);
            $table->string('peso_litro')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Migrate data
        if (Schema::hasTable('laboratorio_insumos') && Schema::hasTable('insumos')) {
            $oldRecords = DB::table('laboratorio_insumos')
                ->join('insumos', 'laboratorio_insumos.insumo_id', '=', 'insumos.id')
                ->select('laboratorio_insumos.*', 'insumos.slug')
                ->get();

            foreach ($oldRecords as $row) {
                $data = [
                    'fecha' => $row->fecha,
                    'preparacion_archivo_contramuestra' => $row->preparacion_archivo_contramuestra,
                    'observaciones' => $row->observaciones,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
                
                if ($row->slug === 'sulfato') {
                    $data['residuo_insoluble'] = $row->residuo_insoluble;
                    $data['oxido_ferroso'] = $row->oxido_ferroso;
                    $data['oxido_ferrico'] = $row->oxido_ferrico;
                    $data['oxido_aluminio'] = $row->oxido_aluminio;
                    $data['oxidos_utiles'] = $row->oxidos_utiles;
                    $data['manganeso'] = $row->manganeso;
                    $data['densidad_20c'] = $row->densidad_20c;
                    DB::table('insumo_sulfatos')->insert($data);
                } elseif ($row->slug === 'hipoclorito') {
                    $data['cloro_activo'] = $row->cloro_activo;
                    $data['densidad_20c'] = $row->densidad_20c;
                    DB::table('insumo_hipocloritos')->insert($data);
                } elseif ($row->slug === 'poliamina') {
                    $data['densidad_20c'] = $row->densidad_20c;
                    DB::table('insumo_poliaminas')->insert($data);
                } elseif ($row->slug === 'cal_hidraulica') {
                    $data['peso_litro'] = $row->peso_litro;
                    DB::table('insumo_cales')->insert($data);
                }
            }

            Schema::dropIfExists('laboratorio_insumos');
            Schema::dropIfExists('insumos');
        }
    }

    public function down()
    {
        Schema::dropIfExists('insumo_sulfatos');
        Schema::dropIfExists('insumo_hipocloritos');
        Schema::dropIfExists('insumo_poliaminas');
        Schema::dropIfExists('insumo_cales');
    }
};
