<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use App\Http\Requests\StorePurchaseOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = PurchaseOrder::with('supplier', 'user');

        if ($user->hasRole('supplier')) {
            $query->whereHas('supplier', fn($q) => $q);
        }

        $purchaseOrders = $query->latest()->paginate(10);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('is_active', true)->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['po_number'] = 'PO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $data['subtotal'] = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
            $data['total'] = $data['subtotal'] + ($data['tax'] ?? 0);

            $po = PurchaseOrder::create($data);

            foreach ($data['items'] as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase Order berhasil dibuat.');
        });
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'user', 'items.product');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return back()->with('error', 'PO tidak dapat diedit karena status saat ini: ' . $purchaseOrder->status);
        }
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::where('is_active', true)->get();
        $purchaseOrder->load('items.product');
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(StorePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['draft', 'sent'])) {
            return back()->with('error', 'PO tidak dapat diubah.');
        }

        return DB::transaction(function () use ($request, $purchaseOrder) {
            $data = $request->validated();
            $data['subtotal'] = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
            $data['total'] = $data['subtotal'] + ($data['tax'] ?? 0);
            $purchaseOrder->update($data);
            $purchaseOrder->items()->delete();

            foreach ($data['items'] as $item) {
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'PO berhasil diperbarui.');
        });
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO dengan status draft yang dapat dihapus.');
        }
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil dihapus.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') return back()->with('error', 'Status tidak valid.');
        $purchaseOrder->update(['status' => 'sent']);
        return back()->with('success', 'PO berhasil dikirim ke supplier.');
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'sent') return back()->with('error', 'Status tidak valid.');
        $purchaseOrder->update(['status' => 'confirmed']);
        return back()->with('success', 'PO berhasil dikonfirmasi.');
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'confirmed') return back()->with('error', 'Status tidak valid.');

        return DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->load('items.product');

            foreach ($purchaseOrder->items as $item) {
                $received = $item->received_quantity ?? 0;
                $remaining = $item->quantity - $received;
                if ($remaining > 0) {
                    $item->increment('received_quantity', $remaining);
                    $item->product()->increment('stock_quantity', $remaining);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'type' => 'in',
                        'quantity' => $remaining,
                        'reference_type' => PurchaseOrder::class,
                        'reference_id' => $purchaseOrder->id,
                        'notes' => 'Penerimaan dari PO: ' . $purchaseOrder->po_number,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $purchaseOrder->update(['status' => 'received']);
            return back()->with('success', 'Barang berhasil diterima dan stok diperbarui.');
        });
    }

    public function complete(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'received') return back()->with('error', 'Status tidak valid.');
        $purchaseOrder->update(['status' => 'completed']);
        return back()->with('success', 'PO berhasil diselesaikan.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'PO sudah selesai atau dibatalkan.');
        }
        $purchaseOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'PO berhasil dibatalkan.');
    }
}
