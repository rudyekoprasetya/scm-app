<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tracking Pengiriman') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Kembali ke Pengiriman</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold mb-2">Tracking: {{ $shipment->shipment_number }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $shipment->carrier }} - {{ $shipment->tracking_number ?? '-' }}</p>

                    <div class="space-y-4">
                        @forelse($events as $event)
                        <div class="flex gap-4 border-l-2 border-indigo-300 pl-4">
                            <div class="flex-1">
                                <div class="text-sm text-gray-500">{{ $event->timestamp ? $event->timestamp->format('d/m/Y H:i') : '-' }}</div>
                                <div>
                                    @php
                                        $colors = ['pending' => 'bg-gray-100 text-gray-700', 'picked_up' => 'bg-yellow-100 text-yellow-700', 'in_transit' => 'bg-blue-100 text-blue-700', 'delivered' => 'bg-green-100 text-green-700', 'failed' => 'bg-red-100 text-red-700'];
                                        $labels = ['pending' => 'Pending', 'picked_up' => 'Diambil', 'in_transit' => 'Dalam Perjalanan', 'delivered' => 'Terkirim', 'failed' => 'Gagal'];
                                    @endphp
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
        </div>
    </div>
</x-app-layout>
