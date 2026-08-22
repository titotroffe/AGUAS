<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('laboratorio_agua_cruda');
        Schema::dropIfExists('laboratorio_producto_terminados');
        Schema::dropIfExists('laboratorio_pozos');
    }

    public function down()
    {
        // No down migration provided since we are migrating to EAV and abandoning these schemas
    }
};
