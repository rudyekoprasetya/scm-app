<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Total Supplier') }}</div>
                        <div class="text-3xl font-bold mt-2">{{ $data['totalSuppliers'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Total Produk') }}</div>
                        <div class="text-3xl font-bold mt-2">{{ $data['totalProducts'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('PO Pending') }}</div>
                        <div class="text-3xl font-bold mt-2 text-yellow-600">{{ $data['pendingPOs'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Pesanan Aktif') }}</div>
                        <div class="text-3xl font-bold mt-2 text-blue-600">{{ $data['activeOrders'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Stok Menipis') }}</div>
                        <div class="text-3xl font-bold mt-2 text-red-600">{{ $data['lowStockProducts'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Pengiriman Hari Ini') }}</div>
                        <div class="text-3xl font-bold mt-2">{{ $data['todayShipments'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b font-semibold">{{ __('PO Terbaru') }}</div>
                        <div class="p-4">
                            <table class="min-w-full text-sm">
                                <thead><tr class="text-left text-gray-500"><th class="pb-2">No. PO</th><th>Supplier</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach(($data['recentPOs'] ?? []) as $po)
                                    <tr class="border-t">
                                        <td class="py-1"><a href="{{ route('purchase-orders.show', $po) }}" class="text-blue-600 hover:underline">{{ $po->po_number }}</a></td>
                                        <td>{{ $po->supplier->name ?? '-' }}</td>
                                        <td><span class="px-2 py-1 rounded text-xs {{ $po->status === 'draft' ? 'bg-gray-200' : ($po->status === 'sent' ? 'bg-yellow-100' : ($po->status === 'confirmed' ? 'bg-blue-100' : ($po->status === 'received' ? 'bg-green-100' : 'bg-green-200'))) }}">{{ __($po->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b font-semibold">{{ __('Pesanan Terbaru') }}</div>
                        <div class="p-4">
                            <table class="min-w-full text-sm">
                                <thead><tr class="text-left text-gray-500"><th class="pb-2">No. Order</th><th>Pelanggan</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach(($data['recentOrders'] ?? []) as $order)
                                    <tr class="border-t">
                                        <td class="py-1"><a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">{{ $order->order_number }}</a></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td><span class="px-2 py-1 rounded text-xs {{ $order->status === 'pending' ? 'bg-gray-200' : ($order->status === 'confirmed' ? 'bg-blue-100' : ($order->status === 'processing' ? 'bg-yellow-100' : ($order->status === 'shipped' ? 'bg-indigo-100' : ($order->status === 'delivered' ? 'bg-green-100' : 'bg-green-200')))) }}">{{ __($order->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('warehouse'))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Total Produk') }}</div>
                        <div class="text-3xl font-bold mt-2">{{ $data['totalProducts'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Menunggu Diterima') }}</div>
                        <div class="text-3xl font-bold mt-2 text-yellow-600">{{ $data['pendingReceives'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Stok Menipis') }}</div>
                        <div class="text-3xl font-bold mt-2 text-red-600">{{ $data['lowStockProducts'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b font-semibold">{{ __('Mutasi Stok Terbaru') }}</div>
                    <div class="p-4">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-gray-500"><th class="pb-2">Produk</th><th>Tipe</th><th>Qty</th><th>Tanggal</th></tr></thead>
                            <tbody>
                                @foreach(($data['recentMovements'] ?? []) as $m)
                                <tr class="border-t">
                                    <td class="py-1">{{ $m->product->name ?? '-' }}</td>
                                    <td><span class="px-2 py-1 rounded text-xs {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $m->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                                    <td>{{ $m->quantity }}</td>
                                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('courier'))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Pengiriman Pending') }}</div>
                        <div class="text-3xl font-bold mt-2">{{ $data['pendingShipments'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">{{ __('Pengiriman Aktif') }}</div>
                        <div class="text-3xl font-bold mt-2 text-blue-600">{{ $data['activeShipments'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b font-semibold">{{ __('Pengiriman Saya') }}</div>
                    <div class="p-4">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-gray-500"><th class="pb-2">No. Kirim</th><th>Kurir</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach(($data['myShipments'] ?? []) as $s)
                                <tr class="border-t">
                                    <td class="py-1"><a href="{{ route('shipments.show', $s) }}" class="text-blue-600 hover:underline">{{ $s->shipment_number }}</a></td>
                                    <td>{{ $s->carrier }}</td>
                                    <td><span class="px-2 py-1 rounded text-xs">{{ __($s->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
