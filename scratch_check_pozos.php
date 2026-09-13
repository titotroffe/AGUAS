<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mediciones = \App\Models\LabMedicion::with('tipoMedicion')
    ->where('modulo_id', 4)
    ->get();

foreach($mediciones as $m) {
    echo "Pozo: " . $m->pozo_id . " | Frecuencia: " . $m->frecuencia_id . " | Nombre: " . $m->tipoMedicion->nombre . "\n";
}
