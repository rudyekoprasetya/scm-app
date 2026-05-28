<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Produk') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf @method('POST')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Kategori" />
                                <select name="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Nama" />
                                <x-text-input name="name" value="{{ old('name') }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="SKU" />
                                <x-text-input name="sku" value="{{ old('sku') }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('sku')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Satuan" />
                                <x-text-input name="unit" value="{{ old('unit') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Harga Beli" />
                                <x-text-input name="purchase_price" type="number" step="0.01" value="{{ old('purchase_price', 0) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('purchase_price')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Harga Jual" />
                                <x-text-input name="selling_price" type="number" step="0.01" value="{{ old('selling_price', 0) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Stok Awal" />
                                <x-text-input name="stock_quantity" type="number" value="{{ old('stock_quantity', 0) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('stock_quantity')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Batas Stok Minimum" />
                                <x-text-input name="low_stock_threshold" type="number" value="{{ old('low_stock_threshold', 0) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('low_stock_threshold')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Deskripsi" />
                                <textarea name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-1" />
                            </div>
                            <div>
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('products.index') }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
