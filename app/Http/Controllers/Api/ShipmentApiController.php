<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShipmentApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view-shipments');
        return ShipmentResource::collection(
            Shipment::with(['order', 'trackingEvents'])->latest()->paginate(20)
        );
    }

    public function show(Shipment $shipment): ShipmentResource
    {
        $this->authorize('view-shipments');
        $shipment->load(['order', 'trackingEvents']);
        return new ShipmentResource($shipment);
    }

    public function updateTracking(Request $request, Shipment $shipment): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:picked_up,in_transit,delivered,failed',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Update shipment status
        $statusMap = [
            'picked_up' => 'picked_up',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'failed' => 'failed',
        ];

        if (isset($statusMap[$data['status']])) {
            $updateData = ['status' => $statusMap[$data['status']]];
            if ($data['status'] === 'delivered') {
                $updateData['delivered_at'] = now();
            }
            $shipment->update($updateData);
        }

        // Create tracking event
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $data['status'],
            'location' => $data['location'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        $shipment->load('trackingEvents');

        return response()->json(new ShipmentResource($shipment));
    }

    public function trackByNumber(Request $request): JsonResponse
    {
        $request->validate(['tracking_number' => 'required|string']);

        $shipment = Shipment::with(['order', 'trackingEvents'])
            ->where('tracking_number', $request->tracking_number)
            ->orWhere('shipment_number', $request->tracking_number)
            ->first();

        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        return response()->json(new ShipmentResource($shipment));
    }
}
