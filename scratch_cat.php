<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$medicionesConfigPozos = \App\Models\LabMedicion::with('tipoMedicion', 'pozo', 'frecuencia')
    ->where('modulo_id', 4)->where('activo', true)->get()->sortBy(fn($c) => strtolower(\Illuminate\Support\Str::ascii($c->tipoMedicion->nombre)))->values();

$categoriasPozos = $medicionesConfigPozos
    ->groupBy('pozo_id')
    ->map(function($pozoMediciones) {
        return $pozoMediciones->groupBy('frecuencia_id')
            ->map(function($frecuenciaMediciones) {
                return $frecuenciaMediciones->groupBy(fn($c) => $c->tipoMedicion->categoria ?? 'FISICOQUÍMICO')
                    ->map(fn($mediciones) => [
                        'clases_grid' => 'grid-cols-2 md:grid-cols-4',
                        'mediciones' => $mediciones
                    ]);
            });
    });

echo count($categoriasPozos) . " pozos\n";
foreach($categoriasPozos as $pozoId => $freqs) {
    echo "Pozo $pozoId has " . count($freqs) . " freqs\n";
    foreach($freqs as $fId => $cats) {
        echo "  Freq $fId has " . count($cats) . " cats\n";
    }
}

// Test fetching
$pozoId = 1;
$frecuenciaId = 2;
$categorias = $categoriasPozos->get($pozoId)?->get($frecuenciaId);
if ($categorias) {
    echo "Found for Pozo $pozoId and Freq $frecuenciaId!\n";
} else {
    echo "NOT FOUND using get($pozoId) and get($frecuenciaId)!\n";
    // Check if string keys work
    if ($categoriasPozos->has((string)$pozoId)) {
        echo "String key works for Pozo!\n";
    }
}
