<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Purchase Order') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <x-input-label value="Supplier" />
                                <select name="supplier_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1" required>
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Tanggal Order" />
                                <x-text-input name="order_date" type="date" value="{{ old('order_date', $purchaseOrder->order_date?->format('Y-m-d')) }}" class="w-full mt-1" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Tanggal Ekspektasi" />
                                <x-text-input name="expected_date" type="date" value="{{ old('expected_date', $purchaseOrder->expected_date?->format('Y-m-d')) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('expected_date')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="Pajak" />
                                <x-text-input name="tax" type="number" step="0.01" value="{{ old('tax', $purchaseOrder->tax) }}" class="w-full mt-1" />
                                <x-input-error :messages="$errors->get('tax')" class="mt-1" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label value="Catatan" />
                                <textarea name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full mt-1">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold">Item</h3>
                                <button type="button" id="tambah-item" class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Tambah Item</button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200" id="items-table">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($purchaseOrder->items as $i => $item)
                                    <tr class="item-row">
                                        <td class="px-4 py-2">
                                            <select name="items[{{ $i }}][product_id]" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach($products as $p)
                                                <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }} data-price="{{ $p->purchase_price }}">{{ $p->name }} ({{ $p->sku }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-2"><x-text-input name="items[{{ $i }}][quantity]" type="number" min="1" value="{{ $item->quantity }}" class="w-20 text-sm item-qty" /></td>
                                        <td class="px-4 py-2"><x-text-input name="items[{{ $i }}][unit_price]" type="number" step="0.01" value="{{ $item->unit_price }}" class="w-32 text-sm item-price" /></td>
                                        <td class="px-4 py-2 item-subtotal text-sm">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2"><button type="button" class="text-red-600 hover:underline text-sm hapus-item">Hapus</button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"><x-secondary-button type="button">Batal</x-secondary-button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = {{ count($purchaseOrder->items) }};
            document.getElementById('tambah-item').addEventListener('click', function() {
                const tbody = document.querySelector('#items-table tbody');
                const row = document.querySelector('.item-row').cloneNode(true);
                row.querySelectorAll('input, select').forEach(el => {
                    el.name = el.name.replace(/\d+/, itemIndex);
                    if (el.tagName === 'INPUT') el.value = el.type === 'number' ? (el.classList.contains('item-qty') ? '1' : '0') : '';
                    if (el.tagName === 'SELECT') el.value = '';
                });
                row.querySelector('.item-subtotal').textContent = '0';
                tbody.appendChild(row);
                itemIndex++;
            });

            document.querySelector('#items-table').addEventListener('click', function(e) {
                if (e.target.classList.contains('hapus-item')) {
                    const rows = document.querySelectorAll('.item-row');
                    if (rows.length > 1) e.target.closest('.item-row').remove();
                }
            });

            document.querySelector('#items-table').addEventListener('change', function(e) {
                if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price') || e.target.name.includes('product_id')) {
                    const row = e.target.closest('.item-row');
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.item-price').value) || 0;
                    if (e.target.name.includes('product_id') && e.target.selectedOptions[0]?.dataset?.price) {
                        row.querySelector('.item-price').value = e.target.selectedOptions[0].dataset.price;
                    }
                    row.querySelector('.item-subtotal').textContent = (qty * price).toLocaleString('id-ID');
                }
            });
        });
    </script>
</x-app-layout>
