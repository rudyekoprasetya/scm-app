<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $data = [
                'totalSuppliers' => \App\Models\Supplier::count(),
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
                'recentMovements' => StockMovement::with('product')->latest()->take(10)->get(),
            ];
        } elseif ($user->hasRole('supplier')) {
            $data = [
                'myPOs' => PurchaseOrder::whereHas('supplier', function ($q) use ($user) {
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

    public function chartData(): JsonResponse
    {
        $user = Auth::user();

        $charts = [];

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $charts['orderStatus'] = Order::selectRaw("status, count(*) as count")
                ->groupBy('status')->pluck('count', 'status');

            $charts['poStatus'] = PurchaseOrder::selectRaw("status, count(*) as count")
                ->groupBy('status')->pluck('count', 'status');

            $charts['shipmentStatus'] = Shipment::selectRaw("status, count(*) as count")
                ->groupBy('status')->pluck('count', 'status');

            $charts['stockLevels'] = Product::where('is_active', true)
                ->orderBy('stock_quantity', 'desc')
                ->take(10)
                ->get(['name', 'stock_quantity', 'low_stock_threshold']);

            $charts['monthlyOrders'] = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as count")
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')->orderBy('month')
                ->pluck('count', 'month');
        }

        if ($user->hasRole('warehouse')) {
            $charts['stockLevels'] = Product::where('is_active', true)
                ->orderBy('stock_quantity', 'desc')
                ->take(10)
                ->get(['name', 'stock_quantity', 'low_stock_threshold']);

            $charts['stockMovements'] = StockMovement::selectRaw("DATE(created_at) as date, type, sum(quantity) as total")
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date', 'type')->orderBy('date')
                ->get();
        }

        if ($user->hasRole('courier')) {
            $charts['shipmentStatus'] = Shipment::selectRaw("status, count(*) as count")
                ->groupBy('status')->pluck('count', 'status');
        }

        return response()->json($charts);
    }
}
