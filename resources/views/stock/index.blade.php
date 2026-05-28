<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Stok') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="mb-4 flex gap-2 flex-wrap">
                <a href="{{ route('stock.in') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">Stok Masuk</a>
                <a href="{{ route('stock.out') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">Stok Keluar</a>
                <a href="{{ route('stock.alerts') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400">Peringatan Stok</a>
                <a href="{{ route('stock.export.pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500"><i class="fa-solid fa-file-pdf mr-1"></i>PDF</a>
                <a href="{{ route('stock.export.excel') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500"><i class="fa-solid fa-file-excel mr-1"></i>Excel</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($movements as $m)
                            <tr>
                                <td class="px-4 py-3">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $m->product->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $m->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $m->quantity }}</td>
                                <td class="px-4 py-3">{{ $m->notes ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $m->user->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada data mutasi stok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $movements->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
