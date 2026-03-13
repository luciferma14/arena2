<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'evento_id'       => $this->evento_id,
            'asiento_id'      => $this->asiento_id,
            'asiento'         => $this->whenLoaded('asiento', fn() => $this->asiento->nombreCompleto()),
            'estado'          => $this->estado,
            'reservado_hasta' => $this->reservado_hasta?->format('H:i:s'),
            'tiempo_restante' => $this->tiempoRestante(),
            'expirado'        => $this->haExpirado(),
        ];
    }
}
