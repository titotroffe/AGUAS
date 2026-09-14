<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mediciones = \App\Models\LabMedicion::where('modulo_id', 4)->get();
echo "Total mediciones modulo 4: " . $mediciones->count() . "\n";
foreach($mediciones as $m) {
    echo "ID: {$m->id}, Pozo: {$m->pozo_id}, Frecuencia: {$m->frecuencia_id}, Tipo: {$m->tipo_medicion_id}\n";
}
