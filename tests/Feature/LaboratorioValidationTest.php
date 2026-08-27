<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaboratorioValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\EavSeeder::class);
    }

    public function test_insumo_validation_fails_when_no_data_is_loaded(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/laboratorio/insumo', [
                'tipo_insumo' => 1,
                'fecha' => '2026-08-26',
            ]);

        $response->assertSessionHasErrors(['mediciones']);
    }

    public function test_insumo_validation_fails_on_numeric_range_and_text_limit(): void
    {
        $user = User::factory()->create();

        $configInsoluble = \App\Models\LabMedicion::where('modulo_id', 1)
            ->where('insumo_id', 1)
            ->where('tipo_medicion_id', 1)
            ->first();

        $response = $this
            ->actingAs($user)
            ->post('/laboratorio/insumo', [
                'tipo_insumo' => 1,
                'fecha' => '2026-08-26',
                'medicion_' . $configInsoluble->id => -5,
            ]);

        $response->assertSessionHasErrors(['medicion_' . $configInsoluble->id]);
    }

    public function test_agua_cruda_validation_fails_on_ph_out_of_range(): void
    {
        $user = User::factory()->create();

        $configPh = \App\Models\LabMedicion::where('modulo_id', 2)
            ->where('tipo_medicion_id', 18) // pH
            ->first();

        $response = $this
            ->actingAs($user)
            ->post('/laboratorio/agua-cruda', [
                'fecha' => '2026-08-26',
                'medicion_' . $configPh->id => 15.0,
            ]);

        $response->assertSessionHasErrors(['medicion_' . $configPh->id]);
    }

    public function test_agua_cruda_validation_passes_on_correct_values(): void
    {
        $user = User::factory()->create();

        $configPh = \App\Models\LabMedicion::where('modulo_id', 2)
            ->where('tipo_medicion_id', 18) // pH
            ->first();

        $response = $this
            ->actingAs($user)
            ->post('/laboratorio/agua-cruda', [
                'fecha' => '2026-08-26',
                'medicion_' . $configPh->id => 7.2,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/laboratorio');

        $this->assertDatabaseHas('lab_valores', [
            'medicion_id' => $configPh->id,
            'valor' => '7.2',
        ]);
    }
}
