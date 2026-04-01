<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use App\Models\Asiento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_sectores_publicos()
    {
        Sector::factory()->count(3)->create(['activo' => true]);

        $response = $this->getJson('/api/sectores');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_admin_puede_crear_sector()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->postJson('/api/admin/sectores', [
            'nombre' => 'Sector Test',
            'descripcion' => 'Sector de prueba',
            'activo' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sectores', [
            'nombre' => 'Sector Test',
        ]);
    }

    public function test_usuario_normal_no_puede_crear_sector()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->postJson('/api/admin/sectores', [
            'nombre' => 'Sector Test',
            'descripcion' => 'Sector de prueba',
            'activo' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_puede_actualizar_sector()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $sector = Sector::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/admin/sectores/{$sector->id}", [
            'nombre' => 'Sector Actualizado',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sectores', [
            'id' => $sector->id,
            'nombre' => 'Sector Actualizado',
        ]);
    }

    public function test_admin_no_puede_eliminar_sector_con_asientos()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $sector = Sector::factory()->create();
        Asiento::factory()->count(3)->create(['sector_id' => $sector->id]);

        $response = $this->actingAs($admin)->deleteJson("/api/admin/sectores/{$sector->id}");

        $response->assertStatus(400);
    }

    public function test_admin_puede_eliminar_sector_sin_asientos()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $sector = Sector::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/sectores/{$sector->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sectores', [
            'id' => $sector->id,
        ]);
    }
}
