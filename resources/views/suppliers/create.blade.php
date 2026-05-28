<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Supplier') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf @method('POST')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Nama" />
                                <x-text-input name="name" value="{{ old('name') }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Kontak Person" />
                                <x-text-input name="contact_person" value="{{ old('contact_person') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('contact_person')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Email" />
                                <x-text-input name="email" type="email" value="{{ old('email') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Telepon" />
                                <x-text-input name="phone" value="{{ old('phone') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Alamat" />
                                <textarea name="address" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Kota" />
                                <x-text-input name="city" value="{{ old('city') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('city')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Provinsi" />
                                <x-text-input name="province" value="{{ old('province') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('province')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Kode Pos" />
                                <x-text-input name="postal_code" value="{{ old('postal_code') }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('postal_code')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Status" />
                                <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Catatan" />
                                <textarea name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('suppliers.index') }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
