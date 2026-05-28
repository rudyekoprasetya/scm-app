<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Produk') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <table class="min-w-full">
                        <tr><td class="py-2 font-semibold w-48">SKU</td><td>{{ $product->sku }}</td></tr>
                        <tr><td class="py-2 font-semibold">Nama</td><td>{{ $product->name }}</td></tr>
                        <tr><td class="py-2 font-semibold">Kategori</td><td>{{ $product->category->name ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Deskripsi</td><td>{{ $product->description ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Satuan</td><td>{{ $product->unit ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Harga Beli</td><td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td></tr>
                        <tr><td class="py-2 font-semibold">Harga Jual</td><td>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td></tr>
                        <tr><td class="py-2 font-semibold">Stok</td><td><span class="{{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $product->stock_quantity }}</span></td></tr>
                        <tr><td class="py-2 font-semibold">Batas Stok Minimum</td><td>{{ $product->low_stock_threshold }}</td></tr>
                        <tr><td class="py-2 font-semibold">Status</td><td><span class="px-2 py-1 rounded text-xs {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span></td></tr>
                    </table>

                    <div class="flex items-center gap-4 mt-6">
                        <a href="{{ route('products.edit', $product) }}"><x-primary-button type="button">Edit</x-primary-button></a>
                        <a href="{{ route('products.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold mb-4">Riwayat Mutasi Stok</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($product->stockMovements as $m)
                            <tr>
                                <td class="px-4 py-2">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-2"><span class="px-2 py-1 rounded text-xs {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $m->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                                <td class="px-4 py-2">{{ $m->quantity }}</td>
                                <td class="px-4 py-2">{{ $m->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-2 text-center text-gray-500">Belum ada mutasi stok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
