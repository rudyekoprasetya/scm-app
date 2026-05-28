<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
        $this->product = $this->createProduct();
    }

    public function test_admin_can_create_order(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('orders.store'), [
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@email.com',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Jl. Sudirman No. 10',
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 2, 'unit_price' => 15000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['customer_name' => 'Budi Santoso']);
    }

    public function test_order_creation_reduces_stock(): void
    {
        $admin = $this->createAdminUser();
        $initialStock = $this->product->stock_quantity;

        $this->actingAs($admin)->post(route('orders.store'), [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@email.com',
            'shipping_address' => 'Jl. Test',
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 5, 'unit_price' => 15000],
            ],
        ]);

        $this->assertEquals($initialStock - 5, $this->product->fresh()->stock_quantity);
    }

    public function test_order_workflow_from_pending_to_completed(): void
    {
        $admin = $this->createAdminUser();
        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'Test',
            'shipping_address' => 'Jl. Test',
            'order_date' => now(),
            'status' => 'pending',
            'subtotal' => 30000,
            'tax' => 3000,
            'shipping_cost' => 10000,
            'total' => 43000,
            'user_id' => $admin->id,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 15000,
            'subtotal' => 30000,
        ]);

        $this->actingAs($admin)->patch(route('orders.confirm', $order));
        $this->assertEquals('confirmed', $order->fresh()->status);

        $this->actingAs($admin)->patch(route('orders.process', $order));
        $this->assertEquals('processing', $order->fresh()->status);

        $this->actingAs($admin)->patch(route('orders.ship', $order));
        $this->assertEquals('shipped', $order->fresh()->status);

        $this->actingAs($admin)->patch(route('orders.deliver', $order));
        $this->assertEquals('delivered', $order->fresh()->status);

        $this->actingAs($admin)->patch(route('orders.complete', $order));
        $this->assertEquals('completed', $order->fresh()->status);
    }

    public function test_order_cancellation_restores_stock(): void
    {
        $admin = $this->createAdminUser();
        $initialStock = $this->product->stock_quantity;

        // Reduce stock first (simulates what store() does)
        $this->product->decrement('stock_quantity', 3);

        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'customer_name' => 'Test Cancel',
            'shipping_address' => 'Jl. Test',
            'order_date' => now(),
            'status' => 'pending',
            'subtotal' => 30000,
            'total' => 30000,
            'user_id' => $admin->id,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 10000,
            'subtotal' => 30000,
        ]);

        $this->actingAs($admin)->patch(route('orders.cancel', $order));
        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals($initialStock, $this->product->fresh()->stock_quantity);
    }
}
