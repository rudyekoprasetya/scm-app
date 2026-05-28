<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class ShipmentWorkflowTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
        $product = $this->createProduct();
        $this->actingAs($this->createAdminUser());

        $this->order = Order::create([
            'order_number' => 'ORD-SHIP-TEST',
            'customer_name' => 'Test Ship',
            'shipping_address' => 'Jl. Kirim No. 1',
            'order_date' => now(),
            'status' => 'confirmed',
            'subtotal' => 30000,
            'total' => 30000,
            'user_id' => auth()->id(),
        ]);
    }

    public function test_admin_can_create_shipment(): void
    {
        $response = $this->post(route('shipments.store'), [
            'order_id' => $this->order->id,
            'carrier' => 'JNE',
            'tracking_number' => 'JNE00123456',
            'shipping_cost' => 15000,
            'origin' => 'Jakarta',
            'destination' => 'Bandung',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipments', ['carrier' => 'JNE']);
    }

    public function test_shipment_auto_generates_number(): void
    {
        $this->post(route('shipments.store'), [
            'order_id' => $this->order->id,
            'carrier' => 'J&T',
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
        ]);

        $shipment = Shipment::latest()->first();
        $this->assertNotNull($shipment->shipment_number);
        $this->assertStringStartsWith('SHIP-', $shipment->shipment_number);
    }

    public function test_shipment_workflow_pending_to_delivered(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->order->id,
            'shipment_number' => 'SHIP-TEST-001',
            'carrier' => 'SiCepat',
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        $this->patch(route('shipments.pick-up', $shipment));
        $this->assertEquals('picked_up', $shipment->fresh()->status);

        $this->patch(route('shipments.transit', $shipment));
        $this->assertEquals('in_transit', $shipment->fresh()->status);

        $this->patch(route('shipments.deliver', $shipment));
        $this->assertEquals('delivered', $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    public function test_shipment_can_be_marked_failed(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->order->id,
            'shipment_number' => 'SHIP-TEST-002',
            'carrier' => 'Pos Indonesia',
            'status' => 'in_transit',
            'user_id' => auth()->id(),
        ]);

        $this->patch(route('shipments.fail', $shipment));
        $this->assertEquals('failed', $shipment->fresh()->status);
    }

    public function test_tracking_events_are_created(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->order->id,
            'shipment_number' => 'SHIP-TEST-003',
            'carrier' => 'AnterAja',
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        $this->post(route('tracking.store', $shipment), [
            'status' => 'picked_up',
            'location' => 'Jakarta',
            'description' => 'Paket telah diambil',
        ]);

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'location' => 'Jakarta',
        ]);
    }
}
