<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_number' => $this->shipment_number,
            'order_id' => $this->order_id,
            'order' => $this->whenLoaded('order', fn() => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'customer_name' => $this->order->customer_name,
            ]),
            'carrier' => $this->carrier,
            'tracking_number' => $this->tracking_number,
            'status' => $this->status,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'shipping_cost' => (float) $this->shipping_cost,
            'estimated_delivery' => $this->estimated_delivery,
            'delivered_at' => $this->delivered_at,
            'notes' => $this->notes,
            'tracking_events' => TrackingEventResource::collection($this->whenLoaded('trackingEvents')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
