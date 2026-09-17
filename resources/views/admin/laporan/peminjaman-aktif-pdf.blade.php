<html>
<head>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 11px; }
    h2 { margin-bottom: 4px; }
    p.info { color: #555; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
    th { background: #f3f4f6; }
</style>
</head>
<body>
    <h2>Laporan Peminjaman Aktif/Terlambat</h2>
    <p class="info">{{ $filterInfo }} &bull; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Dipinjam Oleh</th>
                <th>Tgl Disetujui</th>
                <th>Estimasi Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                @php
                    $terlambat = $trx->tanggal_kembali_estimasi && $trx->tanggal_kembali_estimasi->isPast();
                @endphp
                <tr>
                    <td>{{ $trx->item->nama_barang ?? '-' }}</td>
                    <td>{{ $trx->user->name ?? '-' }}</td>
                    <td>{{ $trx->approved_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $trx->tanggal_kembali_estimasi?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $terlambat ? 'Terlambat' : 'Aktif' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>