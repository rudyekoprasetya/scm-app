<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Peringatan Stok') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Kembali ke Stok</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Saat Ini</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Minimum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($products as $product)
                            <tr>
                                <td class="px-4 py-3">{{ $product->sku }}</td>
                                <td class="px-4 py-3">{{ $product->name }}</td>
                                <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                                <td class="px-4 py-3">{{ $product->low_stock_threshold }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ ($product->stock_quantity - $product->low_stock_threshold) < 0 ? 'text-red-600 font-bold' : '' }}">
                                        {{ $product->stock_quantity - $product->low_stock_threshold }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Semua stok dalam kondisi aman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
