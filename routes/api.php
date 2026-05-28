<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\SupplierApiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::get('/tracking', [ShipmentApiController::class, 'trackByNumber'])->name('api.tracking.lookup');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');

    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats'])->name('api.dashboard.stats');

    Route::get('/suppliers', [SupplierApiController::class, 'index'])->name('api.suppliers.index');
    Route::post('/suppliers', [SupplierApiController::class, 'store'])->name('api.suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierApiController::class, 'show'])->name('api.suppliers.show');
    Route::put('/suppliers/{supplier}', [SupplierApiController::class, 'update'])->name('api.suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierApiController::class, 'destroy'])->name('api.suppliers.destroy');

    Route::get('/products/low-stock', [ProductApiController::class, 'lowStock'])->name('api.products.low-stock');
    Route::get('/products', [ProductApiController::class, 'index'])->name('api.products.index');
    Route::get('/products/{product}', [ProductApiController::class, 'show'])->name('api.products.show');

    Route::get('/purchase-orders', [PurchaseOrderApiController::class, 'index'])->name('api.purchase-orders.index');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderApiController::class, 'show'])->name('api.purchase-orders.show');
    Route::patch('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderApiController::class, 'send'])->name('api.purchase-orders.send');
    Route::patch('/purchase-orders/{purchaseOrder}/confirm', [PurchaseOrderApiController::class, 'confirm'])->name('api.purchase-orders.confirm');
    Route::patch('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderApiController::class, 'receive'])->name('api.purchase-orders.receive');
    Route::patch('/purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderApiController::class, 'cancel'])->name('api.purchase-orders.cancel');

    Route::get('/orders', [OrderApiController::class, 'index'])->name('api.orders.index');
    Route::get('/orders/{order}', [OrderApiController::class, 'show'])->name('api.orders.show');
    Route::patch('/orders/{order}/confirm', [OrderApiController::class, 'confirm'])->name('api.orders.confirm');
    Route::patch('/orders/{order}/process', [OrderApiController::class, 'process'])->name('api.orders.process');
    Route::patch('/orders/{order}/ship', [OrderApiController::class, 'ship'])->name('api.orders.ship');
    Route::patch('/orders/{order}/deliver', [OrderApiController::class, 'deliver'])->name('api.orders.deliver');
    Route::patch('/orders/{order}/cancel', [OrderApiController::class, 'cancel'])->name('api.orders.cancel');

    Route::get('/shipments', [ShipmentApiController::class, 'index'])->name('api.shipments.index');
    Route::get('/shipments/{shipment}', [ShipmentApiController::class, 'show'])->name('api.shipments.show');
    Route::post('/shipments/{shipment}/tracking', [ShipmentApiController::class, 'updateTracking'])->name('api.shipments.tracking');

    Route::get('/notifications', [NotificationApiController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [NotificationApiController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [NotificationApiController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});
