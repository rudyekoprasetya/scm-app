<html>
<head>
    <title>Laporan Purchase Order</title>
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
        <h1>Laporan Purchase Order</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. PO</th>
                <th>Supplier</th>
                <th>Tgl Order</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrders as $i => $po)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $po->po_number }}</td>
                <td>{{ $po->supplier->name ?? '-' }}</td>
                <td>{{ $po->order_date->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($po->total, 0, ',', '.') }}</td>
                <td>{{ __($po->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px; color: #888;">Total PO: {{ $purchaseOrders->count() }}</p>
</body>
</html>
