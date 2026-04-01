<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\Asiento;
use App\Models\Precio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AsientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_obtener_asientos_por_evento()
    {
        $evento = Evento::factory()->create();
        $sector = Sector::factory()->create(['activo' => true]);
        Asiento::factory()->count(5)->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'disponible' => true,
        ]);

        $response = $this->getJson("/api/eventos/{$evento->id}/asientos");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_puede_obtener_asientos_por_sector()
    {
        $evento = Evento::factory()->create();
        $sector = Sector::factory()->create(['activo' => true]);
        Asiento::factory()->count(3)->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'disponible' => true,
        ]);

        $response = $this->getJson("/api/eventos/{$evento->id}/sectores/{$sector->id}/asientos");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'sector',
                    'precio',
                    'asientos',
                ],
            ]);
    }

    public function test_sector_no_disponible_devuelve_error()
    {
        $evento = Evento::factory()->create();
        $sector = Sector::factory()->create();

        $response = $this->getJson("/api/eventos/{$evento->id}/sectores/{$sector->id}/asientos");

        $response->assertStatus(400);
    }
}
