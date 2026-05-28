<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Roles & Permissions') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($roles as $role)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $role->name }}</td>
                                <td class="px-4 py-3">
                                    @foreach($role->permissions as $perm)
                                    <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-700 mr-1 mb-1">{{ $perm->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-4 py-3 text-center text-gray-500">Tidak ada data role.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
