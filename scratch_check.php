<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mediciones = \App\Models\LabMedicion::with('tipoMedicion')
    ->where('modulo_id', 5)
    ->where('frecuencia_id', 2)
    ->get();

foreach($mediciones as $m) {
    echo "ID: " . $m->tipo_medicion_id . " | Nombre: " . $m->tipoMedicion->nombre . " | Cat: " . $m->tipoMedicion->categoria . "\n";
}
