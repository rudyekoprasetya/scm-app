<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Kategori') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="mb-4">
                <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Tambah Kategori</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Induk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($categories as $category)
                            <tr>
                                <td class="px-4 py-3">{{ $category->name }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeColors = ['raw_material' => 'bg-orange-100 text-orange-700', 'finished_good' => 'bg-green-100 text-green-700', 'packaging' => 'bg-blue-100 text-blue-700'];
                                        $typeLabels = ['raw_material' => 'Bahan Baku', 'finished_good' => 'Barang Jadi', 'packaging' => 'Kemasan'];
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs {{ $typeColors[$category->type] ?? 'bg-gray-100' }}">{{ $typeLabels[$category->type] ?? $category->type }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $category->parent->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada data kategori.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $categories->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
