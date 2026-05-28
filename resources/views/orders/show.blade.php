<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Pesanan') }}</h2>
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
                        <tr><td class="py-2 font-semibold w-48">No. Order</td><td>{{ $order->order_number }}</td></tr>
                        <tr><td class="py-2 font-semibold">Pelanggan</td><td>{{ $order->customer_name }}</td></tr>
                        <tr><td class="py-2 font-semibold">Email</td><td>{{ $order->customer_email ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Telepon</td><td>{{ $order->customer_phone ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Tanggal</td><td>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Alamat Pengiriman</td><td>{{ $order->shipping_address ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Status</td>
                            <td>
                                @php
                                    $colors = ['pending' => 'bg-gray-100 text-gray-700', 'confirmed' => 'bg-blue-100 text-blue-700', 'processing' => 'bg-yellow-100 text-yellow-700', 'shipped' => 'bg-indigo-100 text-indigo-700', 'delivered' => 'bg-green-100 text-green-700', 'completed' => 'bg-green-200 text-green-800', 'cancelled' => 'bg-red-100 text-red-700'];
                                    $labels = ['pending' => 'Pending', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Diterima', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $colors[$order->status] ?? 'bg-gray-100' }}">{{ $labels[$order->status] ?? $order->status }}</span>
                            </td>
                        </tr>
                        <tr><td class="py-2 font-semibold">Catatan</td><td>{{ $order->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Item Pesanan</h3>
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
                            @foreach($order->items as $item)
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
                        <div>Subtotal: Rp {{ number_format($order->subtotal, 0, ',', '.') }}</div>
                        <div>Pajak: Rp {{ number_format($order->tax, 0, ',', '.') }}</div>
                        <div>Biaya Kirim: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</div>
                        <div class="font-bold text-lg">Total: Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            @if($order->shipments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Pengiriman</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Kirim</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kurir</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No. Resi</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->shipments as $s)
                            <tr>
                                <td class="px-4 py-2"><a href="{{ route('shipments.show', $s) }}" class="text-blue-600 hover:underline">{{ $s->shipment_number }}</a></td>
                                <td class="px-4 py-2">{{ $s->carrier }}</td>
                                <td class="px-4 py-2">{{ $s->tracking_number ?? '-' }}</td>
                                <td class="px-4 py-2"><span class="px-2 py-1 rounded text-xs">{{ $s->status }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-4 flex-wrap">
                @if($order->status === 'pending')
                    <form action="{{ route('orders.confirm', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Konfirmasi</x-primary-button>
                    </form>
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan?')">
                        @csrf @method('PATCH')
                        <x-danger-button>Batalkan</x-danger-button>
                    </form>
                    <a href="{{ route('orders.edit', $order) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                @endif
                @if($order->status === 'confirmed')
                    <form action="{{ route('orders.process', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Proses</x-primary-button>
                    </form>
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan?')">
                        @csrf @method('PATCH')
                        <x-danger-button>Batalkan</x-danger-button>
                    </form>
                    <a href="{{ route('orders.edit', $order) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                @endif
                @if($order->status === 'processing')
                    <form action="{{ route('orders.ship', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Kirim</x-primary-button>
                    </form>
                @endif
                @if($order->status === 'shipped')
                    <form action="{{ route('orders.deliver', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Tandai Terkirim</x-primary-button>
                    </form>
                @endif
                @if($order->status === 'delivered')
                    <form action="{{ route('orders.complete', $order) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Selesaikan</x-primary-button>
                    </form>
                @endif
                <a href="{{ route('orders.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
            </div>
        </div>
    </div>
</x-app-layout>
