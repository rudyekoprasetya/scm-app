<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingEventController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/charts', [DashboardController::class, 'chartData'])->name('dashboard.charts');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class)->only(['index', 'create', 'store']);
    });

    // Supplier Management
    Route::middleware('role:admin,manager,warehouse')->group(function () {
        Route::get('suppliers/export/pdf', [SupplierController::class, 'exportPdf'])->name('suppliers.export.pdf');
        Route::get('suppliers/export/excel', [SupplierController::class, 'exportExcel'])->name('suppliers.export.excel');
        Route::resource('suppliers', SupplierController::class);
    });

    // Purchase Orders (supplier can view/create, warehouse can receive, admin/manager can approve)
    Route::get('purchase-orders/export/pdf', [PurchaseOrderController::class, 'exportPdf'])->name('purchase-orders.export.pdf');
    Route::get('purchase-orders/export/excel', [PurchaseOrderController::class, 'exportExcel'])->name('purchase-orders.export.excel');
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::prefix('purchase-orders/{purchaseOrder}')->name('purchase-orders.')->group(function () {
        Route::patch('send', [PurchaseOrderController::class, 'send'])->name('send');
        Route::patch('confirm', [PurchaseOrderController::class, 'confirm'])->name('confirm');
        Route::patch('receive', [PurchaseOrderController::class, 'receive'])->name('receive');
        Route::patch('complete', [PurchaseOrderController::class, 'complete'])->name('complete');
        Route::patch('cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
    });

    // Categories & Products (admin, manager, warehouse)
    Route::middleware('role:admin,manager,warehouse')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
        Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::resource('products', ProductController::class);
    });

    // Stock Management
    Route::middleware('role:admin,manager,warehouse')->group(function () {
        Route::get('/stock', [StockMovementController::class, 'index'])->name('stock.index');
        Route::get('/stock/in/{product?}', [StockMovementController::class, 'createIn'])->name('stock.in');
        Route::post('/stock/in', [StockMovementController::class, 'storeIn'])->name('stock.store-in');
        Route::get('/stock/out/{product?}', [StockMovementController::class, 'createOut'])->name('stock.out');
        Route::post('/stock/out', [StockMovementController::class, 'storeOut'])->name('stock.store-out');
        Route::get('/stock/alerts', [StockMovementController::class, 'alerts'])->name('stock.alerts');
        Route::get('/stock/export/pdf', [StockMovementController::class, 'exportPdf'])->name('stock.export.pdf');
        Route::get('/stock/export/excel', [StockMovementController::class, 'exportExcel'])->name('stock.export.excel');
    });

    // Order Management
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
        Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
        Route::resource('orders', OrderController::class);
        Route::prefix('orders/{order}')->name('orders.')->group(function () {
            Route::patch('confirm', [OrderController::class, 'confirm'])->name('confirm');
            Route::patch('process', [OrderController::class, 'process'])->name('process');
            Route::patch('ship', [OrderController::class, 'ship'])->name('ship');
            Route::patch('deliver', [OrderController::class, 'deliver'])->name('deliver');
            Route::patch('complete', [OrderController::class, 'complete'])->name('complete');
            Route::patch('cancel', [OrderController::class, 'cancel'])->name('cancel');
        });
    });

    // Shipments (managed by courier)
    Route::middleware('role:admin,courier')->group(function () {
        Route::get('shipments/export/pdf', [ShipmentController::class, 'exportPdf'])->name('shipments.export.pdf');
        Route::get('shipments/export/excel', [ShipmentController::class, 'exportExcel'])->name('shipments.export.excel');
        Route::resource('shipments', ShipmentController::class);
        Route::prefix('shipments/{shipment}')->name('shipments.')->group(function () {
            Route::patch('pick-up', [ShipmentController::class, 'pickUp'])->name('pick-up');
            Route::patch('transit', [ShipmentController::class, 'inTransit'])->name('transit');
            Route::patch('deliver', [ShipmentController::class, 'deliver'])->name('deliver');
            Route::patch('fail', [ShipmentController::class, 'fail'])->name('fail');
        });
    });

    // Tracking Events
    Route::get('shipments/{shipment}/tracking', [TrackingEventController::class, 'index'])->name('tracking.index');
    Route::post('shipments/{shipment}/tracking', [TrackingEventController::class, 'store'])->name('tracking.store');
});

require __DIR__.'/auth.php';
