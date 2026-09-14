<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$frecuencias = \App\Models\LabFrecuencia::all();
foreach ($frecuencias as $f) {
    echo "ID: {$f->id} - Nombre: {$f->nombre}\n";
}
