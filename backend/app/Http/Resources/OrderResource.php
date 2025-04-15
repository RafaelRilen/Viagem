<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'idPedido' => $this->id,
            'nomeSolicitante' => $this->requester_name,
            'destino' => $this->destination,
            'dataDeIda' => $this->start_date?->format('d/m/Y H:i'),
            'dataDeVolta' => $this->end_date?->format('d/m/Y H:i'),
            'status' => $this->status,
        ];
    }
}
