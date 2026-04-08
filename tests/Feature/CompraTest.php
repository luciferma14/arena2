<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\Asiento;
use App\Models\Precio;
use App\Models\EstadoAsiento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    private function crearReservaCompleta($user, $estado = 'bloqueado', $minutos = 10)
    {
        $evento  = Evento::factory()->create();
        $sector  = Sector::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);

        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'precio'    => 50.00,
        ]);

        return EstadoAsiento::factory()->create([
            'evento_id'      => $evento->id,
            'asiento_id'     => $asiento->id,   // <-- asiento con sector correcto
            'user_id'        => $user->id,
            'estado'         => $estado,
            'reservado_hasta' => now()->addMinutes($minutos),
        ]);
    }

    public function test_usuario_puede_confirmar_compra()
    {
        $user    = User::factory()->create();
        $reserva = $this->crearReservaCompleta($user);

        $response = $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        // Depuración temporal — borra esta línea cuando pase
        if ($response->status() !== 201) {
            dump($response->json());
        }

        $response->assertStatus(201);
        $this->assertDatabaseHas('entradas', [
            'user_id'   => $user->id,
            'evento_id' => $reserva->evento_id,
        ]);
        $this->assertDatabaseHas('estado_asientos', [
            'id'     => $reserva->id,
            'estado' => 'vendido',
        ]);
    }

    public function test_no_puede_comprar_reserva_expirada()
    {
        $user    = User::factory()->create();
        $reserva = EstadoAsiento::factory()->expirado()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $response->assertStatus(400);
    }

    public function test_no_puede_comprar_reserva_de_otro_usuario()
    {
        $user1   = User::factory()->create();
        $user2   = User::factory()->create();
        $reserva = EstadoAsiento::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $response->assertStatus(400);
    }

    public function test_entrada_genera_codigo_qr_automaticamente()
    {
        $user    = User::factory()->create();
        $reserva = $this->crearReservaCompleta($user);

        $this->actingAs($user)->postJson('/api/compras', [
            'reservas' => [$reserva->id],
        ]);

        $entrada = $user->entradas()->first();
        $this->assertNotNull($entrada->codigo_qr);
        $this->assertStringStartsWith('QR-', $entrada->codigo_qr);
        $this->assertEquals(15, strlen($entrada->codigo_qr));
    }
}
