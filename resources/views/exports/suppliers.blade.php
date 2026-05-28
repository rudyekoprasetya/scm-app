<html>
<head>
    <title>Laporan Supplier</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
        h2 { margin: 0; }
        .title { text-align: center; margin-bottom: 20px; }
        .title h1 { font-size: 18px; margin: 0; }
        .title p { font-size: 12px; color: #666; margin: 4px 0; }
    </style>
</head>
<body>
    <div class="title">
        <h1>Laporan Supplier</h1>
        <p>Tanggal: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kontak</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Kota</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->contact_person ?? '-' }}</td>
                <td>{{ $s->email ?? '-' }}</td>
                <td>{{ $s->phone ?? '-' }}</td>
                <td>{{ $s->city ?? '-' }}</td>
                <td>{{ $s->status === 'active' ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px; color: #888;">Total Supplier: {{ $suppliers->count() }}</p>
</body>
</html>
