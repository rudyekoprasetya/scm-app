<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Pengiriman') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('shipments.update', $shipment) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Kurir" />
                                <x-text-input name="carrier" value="{{ old('carrier', $shipment->carrier) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('carrier')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="No. Resi" />
                                <x-text-input name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('tracking_number')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Biaya Kirim" />
                                <x-text-input name="shipping_cost" type="number" step="0.01" value="{{ old('shipping_cost', $shipment->shipping_cost) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('shipping_cost')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Asal" />
                                <x-text-input name="origin" value="{{ old('origin', $shipment->origin) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('origin')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Tujuan" />
                                <x-text-input name="destination" value="{{ old('destination', $shipment->destination) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('destination')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Estimasi Tiba" />
                                <x-text-input name="estimated_delivery_date" type="date" value="{{ old('estimated_delivery_date', $shipment->estimated_delivery_date?->format('Y-m-d')) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('estimated_delivery_date')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Catatan" />
                                <textarea name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('notes', $shipment->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('shipments.show', $shipment) }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
