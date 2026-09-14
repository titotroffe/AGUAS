<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Delete all mediciones for Modulo 4 that are not 29 or 30 (Coliformes)
$deleted = \App\Models\LabMedicion::where('modulo_id', 4)
    ->whereNotIn('tipo_medicion_id', [29, 30])
    ->delete();

echo "Deleted $deleted invalid records for Pozos.\n";
