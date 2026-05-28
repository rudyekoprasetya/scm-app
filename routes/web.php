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
        Route::resource('suppliers', SupplierController::class);
    });

    // Purchase Orders (supplier can view/create, warehouse can receive, admin/manager can approve)
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
    });

    // Order Management
    Route::middleware('role:admin,manager')->group(function () {
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
