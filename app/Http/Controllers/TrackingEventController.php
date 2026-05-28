<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Http\Request;

class TrackingEventController extends Controller
{
    public function index(Shipment $shipment)
    {
        $events = $shipment->trackingEvents()->latest('timestamp')->get();
        return view('tracking.index', compact('shipment', 'events'));
    }

    public function store(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,picked_up,in_transit,delivered,failed',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $data['timestamp'] = now();

        $shipment->trackingEvents()->create($data);

        return back()->with('success', 'Event tracking berhasil ditambahkan.');
    }
}
