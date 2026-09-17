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
    <h2>Laporan Riwayat per Barang</h2>
    <p class="info">{{ $filterInfo }} &bull; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Kode Item</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Pemohon</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                <tr>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $trx->item->nama_barang ?? '-' }}</td>
                    <td>{{ $trx->item->item_id ?? '-' }}</td>
                    <td>{{ $trx->item->category->nama_kategori ?? '-' }}</td>
                    <td>{{ $trx->item->location->nama_lokasi ?? '-' }}</td>
                    <td>{{ $trx->jenis_transaksi }}</td>
                    <td>{{ $statusLabels[$trx->status] ?? ucfirst($trx->status) }}</td>
                    <td>{{ $trx->user->name ?? '-' }}</td>
                    <td>{{ $trx->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>