<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\TrackingEvent;
use App\Http\Requests\StoreShipmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Shipment::with('order', 'user');

        if ($user->hasRole('courier')) {
            $query->where('user_id', $user->id);
        }

        $shipments = $query->latest()->paginate(10);
        return view('shipments.index', compact('shipments'));
    }

    public function create()
    {
        $orders = Order::where('status', 'processing')->orWhere('status', 'shipped')->get();
        return view('shipments.create', compact('orders'));
    }

    public function store(StoreShipmentRequest $request)
    {
        $data = $request->validated();
        $data['shipment_number'] = 'SHIP-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $data['user_id'] = Auth::id();

        $order = Order::findOrFail($data['order_id']);
        $data['destination'] = $data['destination'] ?? $order->shipping_address;
        $data['shipping_cost'] = $data['shipping_cost'] ?? $order->shipping_cost;

        $shipment = Shipment::create($data);

        return redirect()->route('shipments.show', $shipment)->with('success', 'Pengiriman berhasil dibuat.');
    }

    public function show(Shipment $shipment)
    {
        $shipment->load('order', 'user', 'trackingEvents');
        return view('shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {
        if (!in_array($shipment->status, ['pending'])) {
            return back()->with('error', 'Pengiriman tidak dapat diubah.');
        }
        return view('shipments.edit', compact('shipment'));
    }

    public function update(StoreShipmentRequest $request, Shipment $shipment)
    {
        if ($shipment->status !== 'pending') {
            return back()->with('error', 'Pengiriman tidak dapat diubah.');
        }
        $shipment->update($request->validated());
        return redirect()->route('shipments.show', $shipment)->with('success', 'Pengiriman berhasil diperbarui.');
    }

    public function destroy(Shipment $shipment)
    {
        if ($shipment->status !== 'pending') {
            return back()->with('error', 'Hanya pengiriman pending yang dapat dihapus.');
        }
        $shipment->trackingEvents()->delete();
        $shipment->delete();
        return redirect()->route('shipments.index')->with('success', 'Pengiriman berhasil dihapus.');
    }

    // Workflow actions
    public function pickUp(Shipment $shipment)
    {
        if ($shipment->status !== 'pending') return back()->with('error', 'Status tidak valid.');
        $shipment->update(['status' => 'picked_up']);
        $this->addTrackingEvent($shipment, 'picked_up', 'Paket telah diambil dari gudang.');
        return back()->with('success', 'Paket telah diambil.');
    }

    public function inTransit(Shipment $shipment)
    {
        if ($shipment->status !== 'picked_up') return back()->with('error', 'Status tidak valid.');
        $shipment->update(['status' => 'in_transit']);
        $this->addTrackingEvent($shipment, 'in_transit', 'Paket dalam perjalanan.');
        return back()->with('success', 'Status diperbarui: Dalam perjalanan.');
    }

    public function deliver(Shipment $shipment)
    {
        if ($shipment->status !== 'in_transit') return back()->with('error', 'Status tidak valid.');
        $shipment->update(['status' => 'delivered', 'delivered_at' => now()]);
        $this->addTrackingEvent($shipment, 'delivered', 'Paket telah diterima.');
        return back()->with('success', 'Paket telah diterima.');
    }

    public function fail(Shipment $shipment)
    {
        if (!in_array($shipment->status, ['picked_up', 'in_transit'])) return back()->with('error', 'Status tidak valid.');
        $shipment->update(['status' => 'failed']);
        $this->addTrackingEvent($shipment, 'failed', 'Pengiriman gagal.');
        return back()->with('success', 'Pengiriman ditandai gagal.');
    }

    private function addTrackingEvent(Shipment $shipment, string $status, string $description, ?string $location = null)
    {
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'status' => $status,
            'location' => $location,
            'description' => $description,
            'timestamp' => now(),
        ]);
    }
}
