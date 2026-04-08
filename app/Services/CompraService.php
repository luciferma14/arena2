<?php
namespace App\Services;
use App\Models\EstadoAsiento;
use App\Models\Entrada;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function procesarCompra(array $reservasIds, $userId)
    {
        $entradas = [];
        DB::beginTransaction();
        try {
            foreach ($reservasIds as $reservaId) {
                $reserva = $this->obtenerReserva($reservaId, $userId);
                $this->verificarNoExpirada($reserva);
                $precio = $this->obtenerPrecio($reserva);
                $reserva->marcarComoVendido();
                $entrada = $this->crearEntrada($reserva, $precio, $userId);
                $entradas[] = $entrada;
            }
            DB::commit();
            $result = Entrada::with(['evento', 'asiento.sector'])
                ->whereIn('id', array_map(fn($e) => $e->id, $entradas))
                ->get();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function obtenerReserva($reservaId, $userId)
    {
        return EstadoAsiento::where('id', $reservaId)
            ->where('user_id', $userId)
            ->where('estado', 'bloqueado')
            ->with(['evento', 'asiento.sector'])
            ->firstOrFail();
    }

    private function verificarNoExpirada($reserva)
    {
        if ($reserva->haExpirado()) {
            throw new \Exception('Una de las reservas ha expirado');
        }
    }

    private function obtenerPrecio($reserva)
    {
        $precio = $reserva->evento->precioDelSector($reserva->asiento->sector_id);
        if (!$precio) {
            throw new \Exception('No se encontró el precio para el sector');
        }
        return $precio;
    }

    private function crearEntrada($reserva, $precio, $userId)
    {
        return Entrada::create([
            'user_id'       => $userId,
            'evento_id'     => $reserva->evento_id,
            'asiento_id'    => $reserva->asiento_id,
            'precio_pagado' => $precio->precio,
        ]);
    }
}
