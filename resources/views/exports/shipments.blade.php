<html>
<head>
    <title>Laporan Pengiriman</title>
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
        <h1>Laporan Pengiriman</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Kirim</th>
                <th>Kurir</th>
                <th>Resi</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->shipment_number }}</td>
                <td>{{ $s->carrier }}</td>
                <td>{{ $s->tracking_number ?? '-' }}</td>
                <td>{{ $s->origin ?? '-' }}</td>
                <td>{{ $s->destination ?? '-' }}</td>
                <td>{{ __($s->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px; color: #888;">Total Pengiriman: {{ $shipments->count() }}</p>
</body>
</html>
