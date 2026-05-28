<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements' => function ($q) {
            $q->latest()->take(20);
        }]);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function exportPdf()
    {
        $products = Product::with('category')->latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.products', compact('products'));
        return $pdf->download('laporan-produk-'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel()
    {
        $products = Product::with('category')->latest()->get();
        $filename = 'laporan-produk-'.now()->format('Ymd').'.csv';
        $headers = [['No', 'SKU', 'Nama', 'Kategori', 'Stok', 'Satuan', 'Harga Beli', 'Harga Jual', 'Status']];
        foreach ($products as $i => $p) {
            $headers[] = [$i + 1, $p->sku, $p->name, $p->category->name ?? '', $p->stock_quantity, $p->unit, $p->purchase_price, $p->selling_price, $p->is_active ? 'Aktif' : 'Nonaktif'];
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
