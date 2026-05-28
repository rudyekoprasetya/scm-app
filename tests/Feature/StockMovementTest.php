<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
        $this->product = $this->createProduct();
    }

    public function test_warehouse_can_record_stock_in(): void
    {
        $user = $this->createWarehouseUser();
        $initialStock = $this->product->stock_quantity;

        $response = $this->actingAs($user)->post(route('stock.store-in'), [
            'product_id' => $this->product->id,
            'quantity' => 50,
            'notes' => 'Stok masuk test',
        ]);

        $response->assertRedirect();
        $this->assertEquals($initialStock + 50, $this->product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 50,
        ]);
    }

    public function test_warehouse_can_record_stock_out(): void
    {
        $user = $this->createWarehouseUser();
        $initialStock = $this->product->stock_quantity;

        $response = $this->actingAs($user)->post(route('stock.store-out'), [
            'product_id' => $this->product->id,
            'quantity' => 30,
            'notes' => 'Stok keluar test',
        ]);

        $response->assertRedirect();
        $this->assertEquals($initialStock - 30, $this->product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 30,
        ]);
    }

    public function test_stock_out_requires_sufficient_stock(): void
    {
        $user = $this->createWarehouseUser();

        $response = $this->actingAs($user)->post(route('stock.store-out'), [
            'product_id' => $this->product->id,
            'quantity' => 99999,
            'notes' => 'Stok tidak cukup',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_low_stock_alerts_page(): void
    {
        $user = $this->createWarehouseUser();

        $this->product->update(['stock_quantity' => 5, 'low_stock_threshold' => 10]);

        $response = $this->actingAs($user)->get(route('stock.alerts'));

        $response->assertStatus(200);
        $response->assertSee($this->product->name);
    }
}
