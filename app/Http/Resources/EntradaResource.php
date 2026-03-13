<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntradaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'codigo_qr'     => $this->codigo_qr,
            'evento'        => $this->whenLoaded('evento', fn() => $this->evento->nombre),
            'fecha'         => $this->whenLoaded('evento', fn() => $this->evento->fecha->format('d/m/Y')),
            'hora'          => $this->whenLoaded('evento', fn() => $this->evento->hora?->format('H:i')),
            'asiento'       => $this->whenLoaded('asiento', fn() => $this->asiento->nombreCompleto()),
            'precio_pagado' => number_format($this->precio_pagado, 2, ',', '.') . ' €',
            'valida'        => $this->esValida(),
        ];
    }
}
