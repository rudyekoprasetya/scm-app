<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Notifikasi') }}</h2>
            @if(auth()->user()->unreadNotifications->isNotEmpty())
                <a href="{{ route('notifications.read-all') }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    <i class="fa-solid fa-check-double mr-1"></i> Tandai Semua Dibaca
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @forelse($notifications as $notification)
                        @php $data = $notification->data; @endphp
                        <div class="border-b border-gray-200 dark:border-gray-700 py-4 {{ $notification->read_at ? '' : 'bg-blue-50 dark:bg-gray-700 -mx-6 px-6' }}">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                                        @endif
                                        <span class="font-semibold {{ $notification->read_at ? 'text-gray-700 dark:text-gray-300' : 'text-gray-900 dark:text-gray-100' }}">
                                            <i class="fa-solid fa-triangle-exclamation text-yellow-500 mr-1"></i>
                                            Stok Menipis
                                        </span>
                                    </div>
                                    <p class="text-sm mt-1 {{ $notification->read_at ? 'text-gray-500 dark:text-gray-400' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $data['message'] ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if(isset($data['product_id']))
                                        <a href="{{ route('products.show', $data['product_id']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                    @if(!$notification->read_at)
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="text-gray-400 hover:text-gray-600 text-sm">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                            <i class="fa-solid fa-bell-slash text-4xl block mb-2"></i>
                            Tidak ada notifikasi.
                        </p>
                    @endforelse

                    <div class="mt-4">{{ $notifications->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
