<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        $user = auth()->user();

        $data = [];

        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            $data = [
                'total_suppliers' => \App\Models\Supplier::count(),
                'total_products' => Product::count(),
                'pending_pos' => PurchaseOrder::whereIn('status', ['draft', 'sent'])->count(),
                'active_orders' => Order::whereIn('status', ['confirmed', 'processing', 'shipped'])->count(),
                'today_shipments' => Shipment::whereDate('created_at', today())->count(),
                'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('is_active', true)->count(),
            ];
        } elseif ($user->hasRole('courier')) {
            $data = [
                'pending_shipments' => Shipment::where('status', 'pending')->count(),
                'active_shipments' => Shipment::whereIn('status', ['picked_up', 'in_transit'])->count(),
            ];
        } elseif ($user->hasRole('warehouse')) {
            $data = [
                'total_products' => Product::count(),
                'pending_receives' => PurchaseOrder::where('status', 'confirmed')->count(),
                'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('is_active', true)->count(),
            ];
        }

        return response()->json($data);
    }
}
