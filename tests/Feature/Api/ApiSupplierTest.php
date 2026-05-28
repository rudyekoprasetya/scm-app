<?php

namespace Tests\Feature\Api;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class ApiSupplierTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
        $admin = $this->createAdminUser();
        $this->token = $admin->createToken('test')->plainTextToken;
    }

    public function test_can_list_suppliers(): void
    {
        Supplier::factory(3)->create();

        $response = $this->getJson('/api/suppliers', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // collections are wrapped in 'data'
    }

    public function test_can_create_supplier(): void
    {
        $response = $this->postJson('/api/suppliers', [
            'name' => 'PT. Supplier API',
            'contact_person' => 'John',
            'email' => 'john@supplier.com',
            'phone' => '08123456789',
        ], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'PT. Supplier API');
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->putJson('/api/suppliers/' . $supplier->id, [
            'name' => 'Updated Supplier',
        ], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Supplier');
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson('/api/suppliers/' . $supplier->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
