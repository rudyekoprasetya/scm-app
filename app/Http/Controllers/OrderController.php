<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Shipment;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->where('stock_quantity', '>', 0)->get();
        return view('orders.create', compact('products'));
    }

    public function store(StoreOrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $data['order_number'] = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $data['subtotal'] = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
            $data['total'] = $data['subtotal'] + ($data['tax'] ?? 0) + ($data['shipping_cost'] ?? 0);

            $order = Order::create($data);

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock_quantity < $item['quantity']) {
                    return back()->withErrors(['items' => "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock_quantity}"]);
                }

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                // Kurangi stok saat order dibuat
                $product->decrement('stock_quantity', $item['quantity']);
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'notes' => 'Pesanan: ' . $order->order_number,
                    'user_id' => Auth::id(),
                ]);
            }

            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat.');
        });
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product', 'shipments');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Pesanan tidak dapat diubah.');
        }
        $products = Product::where('is_active', true)->get();
        $order->load('items.product');
        return view('orders.edit', compact('order', 'products'));
    }

    public function update(StoreOrderRequest $request, Order $order)
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Pesanan tidak dapat diubah.');
        }

        return DB::transaction(function () use ($request, $order) {
            $data = $request->validated();
            $data['subtotal'] = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);
            $data['total'] = $data['subtotal'] + ($data['tax'] ?? 0) + ($data['shipping_cost'] ?? 0);
            $order->update($data);
            $order->items()->delete();

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil diperbarui.');
        });
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Hanya pesanan pending yang dapat dihapus.');
        }
        $order->items()->delete();
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    // Workflow actions
    public function confirm(Order $order)
    {
        if ($order->status !== 'pending') return back()->with('error', 'Status tidak valid.');
        $order->update(['status' => 'confirmed']);
        return back()->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    public function process(Order $order)
    {
        if ($order->status !== 'confirmed') return back()->with('error', 'Status tidak valid.');
        $order->update(['status' => 'processing']);
        return back()->with('success', 'Pesanan sedang diproses.');
    }

    public function ship(Order $order)
    {
        if ($order->status !== 'processing') return back()->with('error', 'Status tidak valid.');

        $order->update(['status' => 'shipped']);

        // Auto-create shipment
        Shipment::create([
            'order_id' => $order->id,
            'shipment_number' => 'SHIP-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'carrier' => 'TBD',
            'status' => 'pending',
            'origin' => 'Gudang Utama',
            'destination' => $order->shipping_address,
            'shipping_cost' => $order->shipping_cost,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Pesanan dikirim dan shipment dibuat.');
    }

    public function deliver(Order $order)
    {
        if ($order->status !== 'shipped') return back()->with('error', 'Status tidak valid.');
        $order->update(['status' => 'delivered']);
        return back()->with('success', 'Pesanan telah diterima.');
    }

    public function complete(Order $order)
    {
        if ($order->status !== 'delivered') return back()->with('error', 'Status tidak valid.');
        $order->update(['status' => 'completed']);
        return back()->with('success', 'Pesanan selesai.');
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['shipped', 'delivered', 'completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    public function exportPdf()
    {
        $orders = Order::latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.orders', compact('orders'));
        return $pdf->download('laporan-pesanan-'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel()
    {
        $orders = Order::latest()->get();
        $filename = 'laporan-pesanan-'.now()->format('Ymd').'.csv';
        $headers = [['No', 'No. Order', 'Pelanggan', 'Tgl Order', 'Subtotal', 'Pajak', 'Ongkir', 'Total', 'Status']];
        foreach ($orders as $i => $order) {
            $headers[] = [$i + 1, $order->order_number, $order->customer_name, $order->order_date->format('d/m/Y'), $order->subtotal, $order->tax, $order->shipping_cost, $order->total, $order->status];
        }
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            foreach ($headers as $row) { fputcsv($file, $row); }
            fclose($file);
        };
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
}
