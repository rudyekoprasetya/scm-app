<html>
<head>
    <title>Laporan Produk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; margin: 0; }
        .title p { font-size: 12px; color: #666; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="title">
        <h1>Laporan Produk</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name ?? '-' }}</td>
                <td>{{ $p->stock_quantity }} {{ $p->unit }}</td>
                <td>Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                <td>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px; color: #888;">Total Produk: {{ $products->count() }}</p>
</body>
</html>
