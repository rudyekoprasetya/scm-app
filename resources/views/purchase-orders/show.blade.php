<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Purchase Order') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <table class="min-w-full">
                        <tr><td class="py-2 font-semibold w-48">No. PO</td><td>{{ $purchaseOrder->po_number }}</td></tr>
                        <tr><td class="py-2 font-semibold">Supplier</td><td>{{ $purchaseOrder->supplier->name ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Tanggal Order</td><td>{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Tanggal Ekspektasi</td><td>{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Status</td>
                            <td>
                                @php
                                    $colors = ['draft' => 'bg-gray-100 text-gray-700', 'sent' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-blue-100 text-blue-700', 'received' => 'bg-green-100 text-green-700', 'completed' => 'bg-green-200 text-green-800', 'cancelled' => 'bg-red-100 text-red-700'];
                                    $labels = ['draft' => 'Draft', 'sent' => 'Dikirim', 'confirmed' => 'Dikonfirmasi', 'received' => 'Diterima', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $colors[$purchaseOrder->status] ?? 'bg-gray-100' }}">{{ $labels[$purchaseOrder->status] ?? $purchaseOrder->status }}</span>
                            </td>
                        </tr>
                        <tr><td class="py-2 font-semibold">Catatan</td><td>{{ $purchaseOrder->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Item</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="px-4 py-2">{{ $item->product->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 text-right space-y-1">
                        <div>Subtotal: Rp {{ number_format($purchaseOrder->subtotal, 0, ',', '.') }}</div>
                        <div>Pajak: Rp {{ number_format($purchaseOrder->tax, 0, ',', '.') }}</div>
                        <div class="font-bold text-lg">Total: Rp {{ number_format($purchaseOrder->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                @if($purchaseOrder->status === 'draft')
                    <form action="{{ route('purchase-orders.send', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Kirim ke Supplier</x-primary-button>
                    </form>
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                    <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <x-danger-button>Hapus</x-danger-button>
                    </form>
                @endif
                @if($purchaseOrder->status === 'sent')
                    <form action="{{ route('purchase-orders.confirm', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Konfirmasi</x-primary-button>
                    </form>
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                @endif
                @if($purchaseOrder->status === 'confirmed')
                    <form action="{{ route('purchase-orders.receive', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Terima Barang</x-primary-button>
                    </form>
                @endif
                @if($purchaseOrder->status === 'received')
                    <form action="{{ route('purchase-orders.complete', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Selesaikan</x-primary-button>
                    </form>
                @endif
                @if(!in_array($purchaseOrder->status, ['completed', 'cancelled']))
                    <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan?')">
                        @csrf @method('PATCH')
                        <x-danger-button>Batalkan</x-danger-button>
                    </form>
                @endif
                <a href="{{ route('purchase-orders.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
            </div>
        </div>
    </div>
</x-app-layout>
