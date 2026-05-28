<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Purchase Order') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="mb-4 flex gap-2">
                <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Buat PO</a>
                <a href="{{ route('purchase-orders.export.pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500"><i class="fa-solid fa-file-pdf mr-1"></i>PDF</a>
                <a href="{{ route('purchase-orders.export.excel') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500"><i class="fa-solid fa-file-excel mr-1"></i>Excel</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. PO</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($purchaseOrders as $po)
                            <tr>
                                <td class="px-4 py-3">{{ $po->po_number }}</td>
                                <td class="px-4 py-3">{{ $po->supplier->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $po->order_date ? $po->order_date->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $colors = ['draft' => 'bg-gray-100 text-gray-700', 'sent' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-blue-100 text-blue-700', 'received' => 'bg-green-100 text-green-700', 'completed' => 'bg-green-200 text-green-800', 'cancelled' => 'bg-red-100 text-red-700'];
                                        $labels = ['draft' => 'Draft', 'sent' => 'Dikirim', 'confirmed' => 'Dikonfirmasi', 'received' => 'Diterima', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs {{ $colors[$po->status] ?? 'bg-gray-100' }}">{{ $labels[$po->status] ?? $po->status }}</span>
                                </td>
                                <td class="px-4 py-3">Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="text-blue-600 hover:underline">Lihat</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada data purchase order.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $purchaseOrders->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
