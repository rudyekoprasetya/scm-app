<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $data = [
                'totalSuppliers' => Supplier::count(),
                'totalProducts' => Product::count(),
                'pendingPOs' => PurchaseOrder::whereIn('status', ['draft', 'sent'])->count(),
                'activeOrders' => Order::whereIn('status', ['confirmed', 'processing', 'shipped'])->count(),
                'todayShipments' => Shipment::whereDate('created_at', today())->count(),
                'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('is_active', true)->count(),
                'recentPOs' => PurchaseOrder::with('supplier')->latest()->take(5)->get(),
                'recentOrders' => Order::latest()->take(5)->get(),
            ];
        } elseif ($user->hasRole('warehouse')) {
            $data = [
                'totalProducts' => Product::count(),
                'pendingReceives' => PurchaseOrder::where('status', 'confirmed')->count(),
                'lowStockProducts' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('is_active', true)->count(),
                'recentMovements' => \App\Models\StockMovement::with('product')->latest()->take(10)->get(),
            ];
        } elseif ($user->hasRole('supplier')) {
            $data = [
                'myPOs' => PurchaseOrder::whereHas('supplier', function ($q) use ($user) {
                    // Simplified: supplier sees all, filter by related data
                })->count(),
            ];
        } elseif ($user->hasRole('courier')) {
            $data = [
                'pendingShipments' => Shipment::where('status', 'pending')->count(),
                'activeShipments' => Shipment::whereIn('status', ['picked_up', 'in_transit'])->count(),
                'myShipments' => Shipment::where('user_id', $user->id)->latest()->take(10)->get(),
            ];
        } else {
            $data = [];
        }

        return view('dashboard', compact('data'));
    }
}
