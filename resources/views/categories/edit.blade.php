<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Kategori') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Nama" />
                                <x-text-input name="name" value="{{ old('name', $category->name) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Tipe" />
                                <select name="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="raw_material" {{ old('type', $category->type) === 'raw_material' ? 'selected' : '' }}>Bahan Baku</option>
                                    <option value="finished_good" {{ old('type', $category->type) === 'finished_good' ? 'selected' : '' }}>Barang Jadi</option>
                                    <option value="packaging" {{ old('type', $category->type) === 'packaging' ? 'selected' : '' }}>Kemasan</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Induk Kategori" />
                                <select name="parent_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Tidak Ada (Induk)</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('parent_id')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('categories.index') }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
