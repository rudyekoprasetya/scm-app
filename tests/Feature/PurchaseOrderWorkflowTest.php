<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class PurchaseOrderWorkflowTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    private Supplier $supplier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
        $this->supplier = $this->createSupplier();
        $this->product = $this->createProduct();
    }

    public function test_admin_can_create_po(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 10, 'unit_price' => 10000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchase_orders', ['supplier_id' => $this->supplier->id]);
    }

    public function test_po_workflow_from_draft_to_completed(): void
    {
        $admin = $this->createAdminUser();
        $po = PurchaseOrder::create([
            'po_number' => 'PO-TEST-001',
            'supplier_id' => $this->supplier->id,
            'user_id' => $admin->id,
            'order_date' => now(),
            'status' => 'draft',
            'subtotal' => 100000,
            'tax' => 10000,
            'total' => 110000,
        ]);
        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_price' => 10000,
            'subtotal' => 100000,
        ]);

        // Send
        $this->actingAs($admin)->patch(route('purchase-orders.send', $po));
        $this->assertEquals('sent', $po->fresh()->status);

        // Confirm
        $this->actingAs($admin)->patch(route('purchase-orders.confirm', $po));
        $this->assertEquals('confirmed', $po->fresh()->status);

        // Receive
        $this->actingAs($admin)->patch(route('purchase-orders.receive', $po));
        $this->assertEquals('received', $po->fresh()->status);
        $this->assertEquals(110, $this->product->fresh()->stock_quantity);

        // Complete
        $this->actingAs($admin)->patch(route('purchase-orders.complete', $po));
        $this->assertEquals('completed', $po->fresh()->status);
    }

    public function test_po_can_be_cancelled(): void
    {
        $admin = $this->createAdminUser();
        $po = PurchaseOrder::create([
            'po_number' => 'PO-TEST-002',
            'supplier_id' => $this->supplier->id,
            'user_id' => $admin->id,
            'order_date' => now(),
            'status' => 'draft',
            'subtotal' => 0,
            'total' => 0,
        ]);

        $this->actingAs($admin)->patch(route('purchase-orders.cancel', $po));
        $this->assertEquals('cancelled', $po->fresh()->status);
    }

    public function test_po_auto_generates_number(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'order_date' => now()->format('Y-m-d'),
            'items' => [
                ['product_id' => $this->product->id, 'quantity' => 5, 'unit_price' => 20000],
            ],
        ]);

        $po = PurchaseOrder::latest()->first();
        $this->assertNotNull($po->po_number);
        $this->assertStringStartsWith('PO-', $po->po_number);
    }
}
