<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'status' => $this->status,
            'location' => $this->location,
            'description' => $this->description,
            'occurred_at' => $this->occurred_at ?? $this->created_at,
            'created_at' => $this->created_at,
        ];
    }
}
