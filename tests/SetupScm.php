<?php

namespace Tests;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

trait SetupScm
{
    protected function setUpScm(): void
    {
        $this->artisan('db:seed', ['--class' => RoleAndPermissionSeeder::class]);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function createAdminUser(): User
    {
        $user = User::factory()->create(['email' => 'admin@test.com']);
        $user->assignRole('admin');
        return $user;
    }

    protected function createWarehouseUser(): User
    {
        $user = User::factory()->create(['email' => 'warehouse@test.com']);
        $user->assignRole('warehouse');
        return $user;
    }

    protected function createCourierUser(): User
    {
        $user = User::factory()->create(['email' => 'courier@test.com']);
        $user->assignRole('courier');
        return $user;
    }

    protected function createSupplier(): Supplier
    {
        return Supplier::create([
            'name' => 'PT Supplier Test',
            'contact_person' => 'Test Contact',
            'email' => 'supplier@test.com',
            'phone' => '08123456789',
            'address' => 'Jl. Test No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);
    }

    protected function createCategory(): Category
    {
        return Category::create([
            'name' => 'Kategori Test',
            'type' => 'finished_good',
        ]);
    }

    protected function createProduct(Category $category = null): Product
    {
        return Product::create([
            'category_id' => $category ? $category->id : $this->createCategory()->id,
            'name' => 'Produk Test',
            'sku' => 'SKU-' . strtoupper(uniqid()),
            'unit' => 'pcs',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock_quantity' => 100,
            'low_stock_threshold' => 10,
            'is_active' => true,
        ]);
    }
}
