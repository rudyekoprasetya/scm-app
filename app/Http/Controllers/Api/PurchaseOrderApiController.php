<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PurchaseOrderApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view-purchase-orders');
        return PurchaseOrderResource::collection(
            PurchaseOrder::with(['supplier', 'items.product'])->latest()->paginate(20)
        );
    }

    public function show(PurchaseOrder $purchaseOrder): PurchaseOrderResource
    {
        $this->authorize('view-purchase-orders');
        $purchaseOrder->load(['supplier', 'items.product']);
        return new PurchaseOrderResource($purchaseOrder);
    }

    public function send(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('send-purchase-orders');
        if ($purchaseOrder->status !== 'draft') {
            return response()->json(['message' => 'Only draft PO can be sent.'], 422);
        }
        $purchaseOrder->update(['status' => 'sent']);
        return response()->json(new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])));
    }

    public function confirm(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('confirm-purchase-orders');
        if ($purchaseOrder->status !== 'sent') {
            return response()->json(['message' => 'Only sent PO can be confirmed.'], 422);
        }
        $purchaseOrder->update(['status' => 'confirmed']);
        return response()->json(new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])));
    }

    public function receive(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('receive-purchase-orders');
        if ($purchaseOrder->status !== 'confirmed') {
            return response()->json(['message' => 'Only confirmed PO can be received.'], 422);
        }
        $purchaseOrder->update(['status' => 'received']);
        return response()->json(new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])));
    }

    public function cancel(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $this->authorize('cancel-purchase-orders');
        if (in_array($purchaseOrder->status, ['received', 'completed'])) {
            return response()->json(['message' => 'Cannot cancel received/completed PO.'], 422);
        }
        $purchaseOrder->update(['status' => 'cancelled']);
        return response()->json(new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product'])));
    }
}
