<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Pengiriman') }}</h2>
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
                        <tr><td class="py-2 font-semibold w-48">No. Kirim</td><td>{{ $shipment->shipment_number }}</td></tr>
                        <tr><td class="py-2 font-semibold">No. Order</td><td><a href="{{ route('orders.show', $shipment->order) }}" class="text-blue-600 hover:underline">{{ $shipment->order->order_number ?? '-' }}</a></td></tr>
                        <tr><td class="py-2 font-semibold">Pelanggan</td><td>{{ $shipment->order->customer_name ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Kurir</td><td>{{ $shipment->carrier ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">No. Resi</td><td>{{ $shipment->tracking_number ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Biaya Kirim</td><td>Rp {{ number_format($shipment->shipping_cost ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td class="py-2 font-semibold">Status</td>
                            <td>
                                @php
                                    $colors = ['pending' => 'bg-gray-100 text-gray-700', 'picked_up' => 'bg-yellow-100 text-yellow-700', 'in_transit' => 'bg-blue-100 text-blue-700', 'delivered' => 'bg-green-100 text-green-700', 'failed' => 'bg-red-100 text-red-700'];
                                    $labels = ['pending' => 'Pending', 'picked_up' => 'Diambil', 'in_transit' => 'Dalam Perjalanan', 'delivered' => 'Terkirim', 'failed' => 'Gagal'];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $colors[$shipment->status] ?? 'bg-gray-100' }}">{{ $labels[$shipment->status] ?? $shipment->status }}</span>
                            </td>
                        </tr>
                        <tr><td class="py-2 font-semibold">Asal</td><td>{{ $shipment->origin ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Tujuan</td><td>{{ $shipment->destination ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Estimasi Tiba</td><td>{{ $shipment->estimated_delivery_date ? $shipment->estimated_delivery_date->format('d/m/Y') : '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Terkirim Pada</td><td>{{ $shipment->delivered_at ? $shipment->delivered_at->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Catatan</td><td>{{ $shipment->notes ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Timeline Tracking</h3>
                    <div class="space-y-4">
                        @forelse($shipment->trackingEvents->sortByDesc('timestamp') as $event)
                        <div class="flex gap-4 border-l-2 border-indigo-300 pl-4">
                            <div class="flex-1">
                                <div class="text-sm text-gray-500">{{ $event->timestamp ? $event->timestamp->format('d/m/Y H:i') : '-' }}</div>
                                <div>
                                    <span class="px-2 py-1 rounded text-xs {{ $colors[$event->status] ?? 'bg-gray-100' }}">{{ $labels[$event->status] ?? $event->status }}</span>
                                </div>
                                <div class="text-sm mt-1">{{ $event->description }}</div>
                                @if($event->location)<div class="text-xs text-gray-400">Lokasi: {{ $event->location }}</div>@endif
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">Belum ada event tracking.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Tambah Event Tracking</h3>
                    <form action="{{ route('tracking.store', $shipment) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Status" />
                                <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="pending">Pending</option>
                                    <option value="picked_up">Diambil</option>
                                    <option value="in_transit">Dalam Perjalanan</option>
                                    <option value="delivered">Terkirim</option>
                                    <option value="failed">Gagal</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Lokasi" />
                                <x-text-input name="location" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('location')" class="mt-1" />
                            </div>
                            <div class="md:col-span-1">
                                <x-input-label value="Deskripsi" />
                                <x-text-input name="description" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('description')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-primary-button>Tambah Event</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                @if($shipment->status === 'pending')
                    <form action="{{ route('shipments.pick-up', $shipment) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Ambil</x-primary-button>
                    </form>
                    <a href="{{ route('shipments.edit', $shipment) }}"><x-secondary-button type="button">Edit</x-secondary-button></a>
                    <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <x-danger-button>Hapus</x-danger-button>
                    </form>
                @endif
                @if($shipment->status === 'picked_up')
                    <form action="{{ route('shipments.in-transit', $shipment) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Dalam Perjalanan</x-primary-button>
                    </form>
                    <form action="{{ route('shipments.fail', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Tandai gagal?')">
                        @csrf @method('PATCH')
                        <x-danger-button>Gagal</x-danger-button>
                    </form>
                @endif
                @if($shipment->status === 'in_transit')
                    <form action="{{ route('shipments.deliver', $shipment) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <x-primary-button>Terkirim</x-primary-button>
                    </form>
                    <form action="{{ route('shipments.fail', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Tandai gagal?')">
                        @csrf @method('PATCH')
                        <x-danger-button>Gagal</x-danger-button>
                    </form>
                @endif
                <a href="{{ route('shipments.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                <a href="{{ route('tracking.index', $shipment) }}"><x-secondary-button type="button">Lihat Tracking</x-secondary-button></a>
            </div>
        </div>
    </div>
</x-app-layout>
