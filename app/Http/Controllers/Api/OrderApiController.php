<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view-orders');
        return OrderResource::collection(
            Order::with('items.product')->latest()->paginate(20)
        );
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view-orders');
        $order->load('items.product');
        return new OrderResource($order);
    }

    public function confirm(Order $order): JsonResponse
    {
        $this->authorize('confirm-orders');
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Only pending orders can be confirmed.'], 422);
        }
        $order->update(['status' => 'confirmed']);
        return response()->json(new OrderResource($order->load('items.product')));
    }

    public function process(Order $order): JsonResponse
    {
        $this->authorize('process-orders');
        if ($order->status !== 'confirmed') {
            return response()->json(['message' => 'Only confirmed orders can be processed.'], 422);
        }
        $order->update(['status' => 'processing']);
        return response()->json(new OrderResource($order->load('items.product')));
    }

    public function ship(Order $order): JsonResponse
    {
        $this->authorize('ship-orders');
        if ($order->status !== 'processing') {
            return response()->json(['message' => 'Only processing orders can be shipped.'], 422);
        }
        $order->update(['status' => 'shipped']);
        return response()->json(new OrderResource($order->load('items.product')));
    }

    public function deliver(Order $order): JsonResponse
    {
        $this->authorize('deliver-orders');
        if ($order->status !== 'shipped') {
            return response()->json(['message' => 'Only shipped orders can be delivered.'], 422);
        }
        $order->update(['status' => 'delivered']);
        return response()->json(new OrderResource($order->load('items.product')));
    }

    public function cancel(Order $order): JsonResponse
    {
        $this->authorize('cancel-orders');
        if (in_array($order->status, ['delivered', 'completed'])) {
            return response()->json(['message' => 'Cannot cancel delivered/completed orders.'], 422);
        }
        $order->update(['status' => 'cancelled']);
        return response()->json(new OrderResource($order->load('items.product')));
    }
}
