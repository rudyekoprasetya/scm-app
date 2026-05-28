<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    public function exportPdf()
    {
        $suppliers = Supplier::latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.suppliers', compact('suppliers'));
        return $pdf->download('laporan-supplier-'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel()
    {
        $suppliers = Supplier::latest()->get();
        $filename = 'laporan-supplier-'.now()->format('Ymd').'.csv';
        $headers = [['No', 'Nama', 'Kontak', 'Email', 'Telepon', 'Kota', 'Provinsi', 'Status']];
        foreach ($suppliers as $i => $s) {
            $headers[] = [$i + 1, $s->name, $s->contact_person ?? '', $s->email ?? '', $s->phone ?? '', $s->city ?? '', $s->province ?? '', $s->status === 'active' ? 'Aktif' : 'Nonaktif'];
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
