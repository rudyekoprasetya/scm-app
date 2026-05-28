<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Role management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // Supplier management
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-purchase-orders',
            'create-purchase-orders',
            'edit-purchase-orders',
            'delete-purchase-orders',
            'approve-purchase-orders',
            'receive-purchase-orders',
            
            // Inventory management
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'view-stock',
            'create-stock-in',
            'create-stock-out',
            'view-low-stock-alerts',
            
            // Order management
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'process-orders',
            'ship-orders',
            
            // Logistics management
            'view-shipments',
            'create-shipments',
            'edit-shipments',
            'delete-shipments',
            'update-shipment-status',
            'view-tracking',
            
            // Dashboard and reports
            'view-dashboard',
            'view-reports',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $admin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            []
        );
        
        // Admin gets all permissions
        $admin->syncPermissions(Permission::all());

        $manager = Role::updateOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            []
        );
        
        // Manager can view almost everything but cannot delete critical data
        $manager->syncPermissions([
            'view-users',
            'view-roles',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'view-purchase-orders',
            'create-purchase-orders',
            'edit-purchase-orders',
            'approve-purchase-orders',
            'receive-purchase-orders',
            'view-categories',
            'view-products',
            'create-products',
            'edit-products',
            'view-stock',
            'create-stock-in',
            'create-stock-out',
            'view-low-stock-alerts',
            'view-orders',
            'create-orders',
            'edit-orders',
            'process-orders',
            'view-shipments',
            'create-shipments',
            'edit-shipments',
            'update-shipment-status',
            'view-tracking',
            'view-dashboard',
            'view-reports',
        ]);

        $supplier = Role::updateOrCreate(
            ['name' => 'supplier', 'guard_name' => 'web'],
            []
        );
        
        // Supplier can only manage their own purchase orders
        $supplier->syncPermissions([
            'view-purchase-orders',
            'create-purchase-orders',
            'edit-purchase-orders',
            'view-dashboard', // limited view
        ]);

        $warehouse = Role::updateOrCreate(
            ['name' => 'warehouse', 'guard_name' => 'web'],
            []
        );
        
        // Warehouse staff manages inventory and receives goods
        $warehouse->syncPermissions([
            'view-suppliers',
            'view-purchase-orders',
            'edit-purchase-orders', // only to receive/update status
            'receive-purchase-orders',
            'view-categories',
            'view-products',
            'edit-products',
            'view-stock',
            'create-stock-in',
            'create-stock-out',
            'view-low-stock-alerts',
            'view-orders', // to prepare for shipping
            'view-dashboard',
        ]);

        $courier = Role::updateOrCreate(
            ['name' => 'courier', 'guard_name' => 'web'],
            []
        );
        
        // Courier/logistics handles shipping and tracking
        $courier->syncPermissions([
            'view-orders', // to see what needs to be shipped
            'ship-orders',
            'view-shipments',
            'create-shipments',
            'edit-shipments',
            'update-shipment-status',
            'view-tracking',
            'view-dashboard',
        ]);

        // Create admin user if not exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@scm.local'],
            [
                'name' => 'Administrator',
                'email' => 'admin@scm.local',
                'password' => bcrypt('admin123'), // change in production!
            ]
        );
        
        // Assign admin role to admin user
        $adminUser->assignRole('admin');
    }
}
