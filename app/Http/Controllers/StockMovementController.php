<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with('product', 'user')->latest()->paginate(15);
        return view('stock.index', compact('movements'));
    }

    public function createIn(Product $product = null)
    {
        $products = Product::where('is_active', true)->get();
        return view('stock.in', compact('products', 'product'));
    }

    public function storeIn(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data) {
            StockMovement::create([
                'product_id' => $data['product_id'],
                'type' => 'in',
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? 'Stok masuk manual',
                'user_id' => Auth::id(),
            ]);
            Product::findOrFail($data['product_id'])->increment('stock_quantity', $data['quantity']);
        });

        return redirect()->route('stock.index')->with('success', 'Stok masuk berhasil dicatat.');
    }

    public function createOut(Product $product = null)
    {
        $products = Product::where('is_active', true)->get();
        return view('stock.out', compact('products', 'product'));
    }

    public function storeOut(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock_quantity < $data['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Stok saat ini: ' . $product->stock_quantity])->withInput();
        }

        DB::transaction(function () use ($data, $product) {
            StockMovement::create([
                'product_id' => $data['product_id'],
                'type' => 'out',
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? 'Stok keluar manual',
                'user_id' => Auth::id(),
            ]);
            $product->decrement('stock_quantity', $data['quantity']);
        });

        return redirect()->route('stock.index')->with('success', 'Stok keluar berhasil dicatat.');
    }

    public function alerts()
    {
        $products = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->paginate(15);

        return view('stock.alerts', compact('products'));
    }
}
