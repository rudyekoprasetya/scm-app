<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class ApiTrackingTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    public function test_public_tracking_by_number(): void
    {
        $this->setUpScm();
        $admin = $this->createAdminUser();
        $product = $this->createProduct();

        $order = Order::create([
            'order_number' => 'ORD-TRACK-001',
            'customer_name' => 'Track Test',
            'shipping_address' => 'Jl. Tracking',
            'order_date' => now(),
            'status' => 'shipped',
            'subtotal' => 10000,
            'total' => 10000,
            'user_id' => $admin->id,
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'shipment_number' => 'SHIP-TRACK-001',
            'carrier' => 'JNE',
            'tracking_number' => 'TRACK123',
            'status' => 'in_transit',
            'user_id' => $admin->id,
        ]);

        $response = $this->getJson('/api/tracking?tracking_number=TRACK123');

        $response->assertStatus(200)
            ->assertJsonPath('tracking_number', 'TRACK123');
    }

    public function test_public_tracking_returns_404_for_unknown(): void
    {
        $response = $this->getJson('/api/tracking?tracking_number=INVALID');

        $response->assertStatus(404);
    }
}
