<?php

namespace Tests\Feature;

use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SetupScm;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase, SetupScm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScm();
    }

    public function test_admin_can_view_suppliers_list(): void
    {
        $admin = $this->createAdminUser();
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('suppliers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('suppliers');
    }

    public function test_admin_can_create_supplier(): void
    {
        $admin = $this->createAdminUser();
        $data = [
            'name' => 'PT Supplier Baru',
            'contact_person' => 'John Doe',
            'email' => 'john@supplier.com',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'status' => 'active',
        ];

        $response = $this->actingAs($admin)->post(route('suppliers.store'), $data);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['email' => 'john@supplier.com']);
    }

    public function test_admin_can_update_supplier(): void
    {
        $admin = $this->createAdminUser();
        $supplier = Supplier::factory()->create(['name' => 'PT Lama']);

        $response = $this->actingAs($admin)->put(route('suppliers.update', $supplier), [
            'name' => 'PT Baru',
            'contact_person' => 'Jane Doe',
            'email' => 'jane@supplier.com',
            'phone' => '081234567891',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Baru']);
    }

    public function test_admin_can_delete_supplier(): void
    {
        $admin = $this->createAdminUser();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_unauthenticated_user_cannot_access_suppliers(): void
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertRedirect(route('login'));
    }
}
