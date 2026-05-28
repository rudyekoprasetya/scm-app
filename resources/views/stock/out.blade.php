<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Stok Keluar') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('stock.store-out') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Produk" />
                                <select name="product_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id', $product?->id) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }}) - Stok: {{ $p->stock_quantity }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Jumlah" />
                                <x-text-input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Catatan" />
                                <textarea name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('stock.index') }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
