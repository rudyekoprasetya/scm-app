<nav x-data="{ open: false, menu: 'dashboard' }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"><i class="fa-solid fa-gauge-high mr-1.5"></i>{{ __('Dashboard') }}</x-nav-link>

                    @can('view-suppliers')
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')"><i class="fa-solid fa-handshake mr-1.5"></i>{{ __('Supplier') }}</x-nav-link>
                    @endcan

                    @can('view-purchase-orders')
                    <x-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')"><i class="fa-solid fa-file-invoice mr-1.5"></i>{{ __('PO') }}</x-nav-link>
                    @endcan

                    @can('view-products')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"><i class="fa-solid fa-box mr-1.5"></i>{{ __('Produk') }}</x-nav-link>
                    @endcan

                    @can('view-categories')
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')"><i class="fa-solid fa-tags mr-1.5"></i>{{ __('Kategori') }}</x-nav-link>
                    @endcan

                    @can('view-stock')
                    <x-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.*')"><i class="fa-solid fa-warehouse mr-1.5"></i>{{ __('Stok') }}</x-nav-link>
                    @endcan

                    @can('view-orders')
                    <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')"><i class="fa-solid fa-cart-shopping mr-1.5"></i>{{ __('Pesanan') }}</x-nav-link>
                    @endcan

                    @can('view-shipments')
                    <x-nav-link :href="route('shipments.index')" :active="request()->routeIs('shipments.*') || request()->routeIs('tracking.*')"><i class="fa-solid fa-truck mr-1.5"></i>{{ __('Kirim') }}</x-nav-link>
                    @endcan

                    @can('view-users')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*') || request()->routeIs('roles.*')"><i class="fa-solid fa-users mr-1.5"></i>{{ __('Pengguna') }}</x-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Dark Mode Toggle --}}
                <button @click="dark = ! dark" class="mr-4 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out" title="Toggle Dark Mode">
                    <template x-if="!dark">
                        <i class="fa-solid fa-moon text-lg"></i>
                    </template>
                    <template x-if="dark">
                        <i class="fa-solid fa-sun text-lg"></i>
                    </template>
                </button>

                {{-- Notification Bell --}}
                <a href="{{ route('notifications.index') }}" class="relative mr-4 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out" title="Notifikasi">
                    <i class="fa-solid fa-bell text-lg"></i>
                    @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] h-[18px]">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="dark = ! dark" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out mr-1" title="Toggle Dark Mode">
                    <template x-if="!dark">
                        <i class="fa-solid fa-moon text-lg"></i>
                    </template>
                    <template x-if="dark">
                        <i class="fa-solid fa-sun text-lg"></i>
                    </template>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"><i class="fa-solid fa-gauge-high mr-2"></i>{{ __('Dashboard') }}</x-responsive-nav-link>
            @can('view-suppliers')<x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')"><i class="fa-solid fa-handshake mr-2"></i>{{ __('Supplier') }}</x-responsive-nav-link>@endcan
            @can('view-purchase-orders')<x-responsive-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')"><i class="fa-solid fa-file-invoice mr-2"></i>{{ __('Purchase Order') }}</x-responsive-nav-link>@endcan
            @can('view-products')<x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"><i class="fa-solid fa-box mr-2"></i>{{ __('Produk') }}</x-responsive-nav-link>@endcan
            @can('view-categories')<x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')"><i class="fa-solid fa-tags mr-2"></i>{{ __('Kategori') }}</x-responsive-nav-link>@endcan
            @can('view-stock')<x-responsive-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.*')"><i class="fa-solid fa-warehouse mr-2"></i>{{ __('Stok') }}</x-responsive-nav-link>@endcan
            @can('view-orders')<x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')"><i class="fa-solid fa-cart-shopping mr-2"></i>{{ __('Pesanan') }}</x-responsive-nav-link>@endcan
            @can('view-shipments')<x-responsive-nav-link :href="route('shipments.index')" :active="request()->routeIs('shipments.*')"><i class="fa-solid fa-truck mr-2"></i>{{ __('Pengiriman') }}</x-responsive-nav-link>@endcan
            @can('view-users')<x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')"><i class="fa-solid fa-users mr-2"></i>{{ __('Pengguna') }}</x-responsive-nav-link>@endcan
        </div>
        <div class="pt-2 pb-1 border-t border-gray-200 dark:border-gray-700">
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                <i class="fa-solid fa-bell mr-2"></i>{{ __('Notifikasi') }}
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
