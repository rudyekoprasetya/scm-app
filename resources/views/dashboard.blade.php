<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 px-4 py-2 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">{{ session('success') }}</div>
            @endif

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="relative overflow-hidden bg-teal-50 border-l-4 border-teal-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-teal-700 font-medium">{{ __('Total Supplier') }}</div>
                            <div class="text-3xl font-bold mt-2 text-teal-900">{{ $data['totalSuppliers'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-handshake absolute right-3 bottom-3 text-5xl text-teal-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-green-50 border-l-4 border-green-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-green-700 font-medium">{{ __('Total Produk') }}</div>
                            <div class="text-3xl font-bold mt-2 text-green-900">{{ $data['totalProducts'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-box absolute right-3 bottom-3 text-5xl text-green-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-orange-50 border-l-4 border-orange-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-orange-700 font-medium">{{ __('PO Pending') }}</div>
                            <div class="text-3xl font-bold mt-2 text-orange-900">{{ $data['pendingPOs'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-file-invoice absolute right-3 bottom-3 text-5xl text-orange-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-indigo-50 border-l-4 border-indigo-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-indigo-700 font-medium">{{ __('Pesanan Aktif') }}</div>
                            <div class="text-3xl font-bold mt-2 text-indigo-900">{{ $data['activeOrders'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-cart-shopping absolute right-3 bottom-3 text-5xl text-indigo-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-red-50 border-l-4 border-red-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-red-700 font-medium">{{ __('Stok Menipis') }}</div>
                            <div class="text-3xl font-bold mt-2 text-red-900">{{ $data['lowStockProducts'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation absolute right-3 bottom-3 text-5xl text-red-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-cyan-50 border-l-4 border-cyan-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-cyan-700 font-medium">{{ __('Pengiriman Hari Ini') }}</div>
                            <div class="text-3xl font-bold mt-2 text-cyan-900">{{ $data['todayShipments'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-truck absolute right-3 bottom-3 text-5xl text-cyan-200 opacity-50"></i>
                    </div>
                </div>

                <div x-data="chartManager('{{ route('dashboard.charts') }}')" x-init="init()" class="space-y-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Status Pesanan') }}</h3>
                            <canvas x-ref="orderStatusChart" height="200"></canvas>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Status PO') }}</h3>
                            <canvas x-ref="poStatusChart" height="200"></canvas>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Status Pengiriman') }}</h3>
                            <canvas x-ref="shipmentStatusChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Stok Produk (Top 10)') }}</h3>
                            <canvas x-ref="stockLevelsChart" height="250"></canvas>
                        </div>
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Pesanan per Bulan') }}</h3>
                            <canvas x-ref="monthlyOrdersChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b dark:border-gray-700 font-semibold dark:text-gray-200">{{ __('PO Terbaru') }}</div>
                        <div class="p-4">
                            <table class="min-w-full text-sm">
                                <thead><tr class="text-left text-gray-500 dark:text-gray-400"><th class="pb-2">No. PO</th><th>Supplier</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach(($data['recentPOs'] ?? []) as $po)
                                    <tr class="border-t dark:border-gray-700 dark:text-gray-300">
                                        <td class="py-1"><a href="{{ route('purchase-orders.show', $po) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $po->po_number }}</a></td>
                                        <td>{{ $po->supplier->name ?? '-' }}</td>
                                        <td><span class="px-2 py-1 rounded text-xs {{ $po->status === 'draft' ? 'bg-gray-200 dark:bg-gray-600' : ($po->status === 'sent' ? 'bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300' : ($po->status === 'confirmed' ? 'bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : ($po->status === 'received' ? 'bg-green-100 dark:bg-green-900 dark:text-green-300' : 'bg-green-200 dark:bg-green-800 dark:text-green-300'))) }}">{{ __($po->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b dark:border-gray-700 font-semibold dark:text-gray-200">{{ __('Pesanan Terbaru') }}</div>
                        <div class="p-4">
                            <table class="min-w-full text-sm">
                                <thead><tr class="text-left text-gray-500 dark:text-gray-400"><th class="pb-2">No. Order</th><th>Pelanggan</th><th>Status</th></tr></thead>
                                <tbody>
                                    @foreach(($data['recentOrders'] ?? []) as $order)
                                    <tr class="border-t dark:border-gray-700 dark:text-gray-300">
                                        <td class="py-1"><a href="{{ route('orders.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $order->order_number }}</a></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td><span class="px-2 py-1 rounded text-xs {{ $order->status === 'pending' ? 'bg-gray-200 dark:bg-gray-600' : ($order->status === 'confirmed' ? 'bg-blue-100 dark:bg-blue-900 dark:text-blue-300' : ($order->status === 'processing' ? 'bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300' : ($order->status === 'shipped' ? 'bg-indigo-100 dark:bg-indigo-900 dark:text-indigo-300' : ($order->status === 'delivered' ? 'bg-green-100 dark:bg-green-900 dark:text-green-300' : 'bg-green-200 dark:bg-green-800 dark:text-green-300')))) }}">{{ __($order->status) }}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('warehouse'))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="relative overflow-hidden bg-green-50 border-l-4 border-green-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-green-700 font-medium">{{ __('Total Produk') }}</div>
                            <div class="text-3xl font-bold mt-2 text-green-900">{{ $data['totalProducts'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-box absolute right-3 bottom-3 text-5xl text-green-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-yellow-50 border-l-4 border-yellow-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-yellow-700 font-medium">{{ __('Menunggu Diterima') }}</div>
                            <div class="text-3xl font-bold mt-2 text-yellow-900">{{ $data['pendingReceives'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-clock absolute right-3 bottom-3 text-5xl text-yellow-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-red-50 border-l-4 border-red-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-red-700 font-medium">{{ __('Stok Menipis') }}</div>
                            <div class="text-3xl font-bold mt-2 text-red-900">{{ $data['lowStockProducts'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation absolute right-3 bottom-3 text-5xl text-red-200 opacity-50"></i>
                    </div>
                </div>

                <div x-data="chartManager('{{ route('dashboard.charts') }}')" x-init="init()" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Stok Produk (Top 10)') }}</h3>
                        <canvas x-ref="stockLevelsChart" height="250"></canvas>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Mutasi Stok (7 Hari)') }}</h3>
                        <canvas x-ref="stockMovementsChart" height="250"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-4 border-b dark:border-gray-700 font-semibold dark:text-gray-200">{{ __('Mutasi Stok Terbaru') }}</div>
                    <div class="p-4">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-gray-500 dark:text-gray-400"><th class="pb-2">Produk</th><th>Tipe</th><th>Qty</th><th>Tanggal</th></tr></thead>
                            <tbody>
                                @foreach(($data['recentMovements'] ?? []) as $m)
                                <tr class="border-t dark:border-gray-700 dark:text-gray-300">
                                    <td class="py-1">{{ $m->product->name ?? '-' }}</td>
                                    <td><span class="px-2 py-1 rounded text-xs {{ $m->type === 'in' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' }}">{{ $m->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                                    <td>{{ $m->quantity }}</td>
                                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(auth()->user()->hasRole('courier'))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="relative overflow-hidden bg-orange-50 border-l-4 border-orange-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-orange-700 font-medium">{{ __('Pengiriman Pending') }}</div>
                            <div class="text-3xl font-bold mt-2 text-orange-900">{{ $data['pendingShipments'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-clock absolute right-3 bottom-3 text-5xl text-orange-200 opacity-50"></i>
                    </div>
                    <div class="relative overflow-hidden bg-blue-50 border-l-4 border-blue-500 shadow-sm sm:rounded-lg p-6">
                        <div class="relative z-10">
                            <div class="text-sm text-blue-700 font-medium">{{ __('Pengiriman Aktif') }}</div>
                            <div class="text-3xl font-bold mt-2 text-blue-900">{{ $data['activeShipments'] ?? 0 }}</div>
                        </div>
                        <i class="fa-solid fa-truck absolute right-3 bottom-3 text-5xl text-blue-200 opacity-50"></i>
                    </div>
                </div>

                <div x-data="chartManager('{{ route('dashboard.charts') }}')" x-init="init()" class="mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4 max-w-sm">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ __('Status Pengiriman') }}</h3>
                        <canvas x-ref="shipmentStatusChart" height="200"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b dark:border-gray-700 font-semibold dark:text-gray-200">{{ __('Pengiriman Saya') }}</div>
                    <div class="p-4">
                        <table class="min-w-full text-sm">
                            <thead><tr class="text-left text-gray-500 dark:text-gray-400"><th class="pb-2">No. Kirim</th><th>Kurir</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach(($data['myShipments'] ?? []) as $s)
                                <tr class="border-t dark:border-gray-700 dark:text-gray-300">
                                    <td class="py-1"><a href="{{ route('shipments.show', $s) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $s->shipment_number }}</a></td>
                                    <td>{{ $s->carrier }}</td>
                                    <td><span class="px-2 py-1 rounded text-xs">{{ __($s->status) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
function chartManager(url) {
    return {
        charts: {},
        polling: null,
        async init() {
            await this.loadCharts();
            this.polling = setInterval(() => this.loadCharts(), 30000);
        },
        destroy() {
            if (this.polling) clearInterval(this.polling);
            Object.values(this.charts).forEach(c => c.destroy());
            this.charts = {};
        },
        async loadCharts() {
            try {
                const res = await fetch(url);
                const data = await res.json();

                const isDark = localStorage.getItem('dark') === 'true';
                const textColor = isDark ? '#9ca3af' : '#6b7280';
                const gridColor = isDark ? '#374151' : '#e5e7eb';
                const colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];

                if (data.orderStatus && this.$refs.orderStatusChart) {
                    this.renderDonut('orderStatusChart', data.orderStatus, textColor, colors);
                }
                if (data.poStatus && this.$refs.poStatusChart) {
                    this.renderDonut('poStatusChart', data.poStatus, textColor, colors);
                }
                if (data.shipmentStatus && this.$refs.shipmentStatusChart) {
                    this.renderDonut('shipmentStatusChart', data.shipmentStatus, textColor, colors);
                }
                if (data.stockLevels && this.$refs.stockLevelsChart) {
                    this.renderBar('stockLevelsChart', data.stockLevels, textColor, gridColor, isDark);
                }
                if (data.monthlyOrders && this.$refs.monthlyOrdersChart) {
                    this.renderLine('monthlyOrdersChart', data.monthlyOrders, textColor, gridColor);
                }
                if (data.stockMovements && this.$refs.stockMovementsChart) {
                    this.renderStockMovements('stockMovementsChart', data.stockMovements, textColor, gridColor);
                }
            } catch (e) {
                console.error('Chart load failed:', e);
            }
        },
        renderDonut(refKey, rawData, textColor, colors) {
            const labels = Object.keys(rawData);
            const values = Object.values(rawData).map(Number);
            if (this.charts[refKey]) this.charts[refKey].destroy();
            const ctx = this.$refs[refKey].getContext('2d');
            this.charts[refKey] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{ data: values, backgroundColor: colors.slice(0, labels.length), borderWidth: 1 }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textColor, font: { size: 10 } } }
                    }
                }
            });
        },
        renderBar(refKey, items, textColor, gridColor, isDark) {
            const labels = items.map(i => i.name.length > 15 ? i.name.substring(0, 15) + '..' : i.name);
            const stock = items.map(i => i.stock_quantity);
            const threshold = items.map(i => i.low_stock_threshold);
            if (this.charts[refKey]) this.charts[refKey].destroy();
            const ctx = this.$refs[refKey].getContext('2d');
            this.charts[refKey] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Stok', data: stock, backgroundColor: '#6366f1', borderRadius: 4 },
                        { label: 'Threshold', data: threshold, backgroundColor: '#ef444480', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 9 } }, grid: { color: gridColor } },
                        y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { color: textColor, font: { size: 10 } } }
                    }
                }
            });
        },
        renderLine(refKey, rawData, textColor, gridColor) {
            const labels = Object.keys(rawData);
            const values = Object.values(rawData).map(Number);
            if (this.charts[refKey]) this.charts[refKey].destroy();
            const ctx = this.$refs[refKey].getContext('2d');
            this.charts[refKey] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pesanan',
                        data: values,
                        borderColor: '#6366f1',
                        backgroundColor: '#6366f120',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#6366f1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } },
                        y: { beginAtZero: true, ticks: { color: textColor, precision: 0 }, grid: { color: gridColor } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        },
        renderStockMovements(refKey, items, textColor, gridColor) {
            const dates = [...new Set(items.map(i => i.date))].sort();
            const byType = {};
            items.forEach(i => {
                if (!byType[i.type]) byType[i.type] = {};
                byType[i.type][i.date] = (byType[i.type][i.date] || 0) + Number(i.total);
            });
            const inData = dates.map(d => byType['in']?.[d] || 0);
            const outData = dates.map(d => byType['out']?.[d] || 0);
            if (this.charts[refKey]) this.charts[refKey].destroy();
            const ctx = this.$refs[refKey].getContext('2d');
            this.charts[refKey] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [
                        { label: 'Masuk', data: inData, backgroundColor: '#10b981', borderRadius: 4 },
                        { label: 'Keluar', data: outData, backgroundColor: '#ef4444', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 9 } }, grid: { color: gridColor } },
                        y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { color: textColor, font: { size: 10 } } }
                    }
                }
            });
        }
    };
}
</script>
