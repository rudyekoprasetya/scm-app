<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Supplier') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full">
                        <tr><td class="py-2 font-semibold w-48">Nama</td><td>{{ $supplier->name }}</td></tr>
                        <tr><td class="py-2 font-semibold">Kontak Person</td><td>{{ $supplier->contact_person ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Email</td><td>{{ $supplier->email ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Telepon</td><td>{{ $supplier->phone ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Alamat</td><td>{{ $supplier->address ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Kota</td><td>{{ $supplier->city ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Provinsi</td><td>{{ $supplier->province ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Kode Pos</td><td>{{ $supplier->postal_code ?? '-' }}</td></tr>
                        <tr><td class="py-2 font-semibold">Status</td><td><span class="px-2 py-1 rounded text-xs {{ $supplier->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">{{ $supplier->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></td></tr>
                        <tr><td class="py-2 font-semibold">Catatan</td><td>{{ $supplier->notes ?? '-' }}</td></tr>
                    </table>

                    <div class="flex items-center gap-4 mt-6">
                        <a href="{{ route('suppliers.edit', $supplier) }}"><x-primary-button type="button">Edit</x-primary-button></a>
                        <a href="{{ route('suppliers.index') }}"><x-secondary-button type="button">Kembali</x-secondary-button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
