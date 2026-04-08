<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CompraService;
use App\Models\User;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\Asiento;
use App\Models\Precio;
use App\Models\EstadoAsiento;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompraServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CompraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompraService();
    }

    private function crearReservaCompleta($user, $minutos = 10)
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
            'asiento_id'     => $asiento->id,
            'user_id'        => $user->id,
            'estado'         => 'bloqueado',
            'reservado_hasta' => now()->addMinutes($minutos),
        ]);
    }

    public function test_puede_procesar_compra()
    {
        $user    = User::factory()->create();
        $reserva = $this->crearReservaCompleta($user);

        $entradas = $this->service->procesarCompra([$reserva->id], $user->id);

        $this->assertCount(1, $entradas);
        $this->assertEquals('vendido', $reserva->fresh()->estado);
    }

    public function test_no_puede_procesar_compra_expirada()
    {
        $user    = User::factory()->create();
        $reserva = EstadoAsiento::factory()->expirado()->create([
            'user_id' => $user->id,
        ]);

        $this->expectException(\Exception::class);
        $this->service->procesarCompra([$reserva->id], $user->id);
    }

    public function test_puede_procesar_compra_multiple()
    {
        $user = User::factory()->create();

        $reservas = collect([
            $this->crearReservaCompleta($user),
            $this->crearReservaCompleta($user),
            $this->crearReservaCompleta($user),
        ]);

        $entradas = $this->service->procesarCompra(
            $reservas->pluck('id')->toArray(),
            $user->id
        );

        $this->assertCount(3, $entradas);
    }

    public function test_rollback_si_falla_una_compra()
    {
        $user     = User::factory()->create();
        $reserva1 = $this->crearReservaCompleta($user);
        $reserva2 = EstadoAsiento::factory()->expirado()->create([
            'user_id' => $user->id,
        ]);

        try {
            $this->service->procesarCompra([$reserva1->id, $reserva2->id], $user->id);
        } catch (\Exception $e) {
            // Esperado
        }

        $this->assertEquals('bloqueado', $reserva1->fresh()->estado);
        $this->assertDatabaseMissing('entradas', ['user_id' => $user->id]);
    }
}
